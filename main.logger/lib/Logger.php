<?php

namespace App\DebugLogger;

use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\ORM\Entity;

require_once(__DIR__ . '/config/config.php');

class Logger
{
    public $logger;

    public function __construct()
    {
        $connection = Application::getConnection();
        if ( $connection->isTableExists('main_logger')) {
            if (Loader::includeModule('main.logger')) {
                
                $query = "SELECT * FROM `main_logger`";
                $result = $connection->query($query);

                while ($row = $result->fetch()) {
                    if ($row['DIR_LOGGER']) {
                        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $row['DIR_LOGGER'])) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . $row['DIR_LOGGER'], 0755, true);
                        }
                        DebugLogger::$logFileDir = $_SERVER['DOCUMENT_ROOT'] . $row['DIR_LOGGER'];
                    } else {
                        DebugLogger::$logFileDir = $_SERVER['DOCUMENT_ROOT'] . DIR_LOGGER;
                    }
                    if ($row['FILE_LOGGER_NAME']) {
                        $this->logger = DebugLogger::instance($row['FILE_LOGGER_NAME'] . '.log');
                    } else {
                        $this->logger = DebugLogger::instance(FILE_LOGGER_NAME . '.log');
                    }
                    if ($row['ACTIVE']) {
                        $active = $row['ACTIVE'] == 'Y' ? true : false;
                        $this->logger->isActive = $active;
                    } else {
                        $this->logger->isActive = LOGGER;
                    }
                }
            }
        }else{
            DebugLogger::$logFileDir = $_SERVER['DOCUMENT_ROOT'] . DIR_LOGGER;
            $this->logger = DebugLogger::instance(FILE_LOGGER_NAME . '.log');
            $this->logger->isActive = LOGGER;
        }
    }

    public function save($array, $method = false, $line = false)
    {
        return $this->logger->save($array, '', $method . ' ' . $line);
    }

    public static function debug($array, $method = false, $line = false)
    {
        return (new Logger)->save($array, $method, $line);
    }

     public static function printR($var)
    {
        static $int = 0;
        echo '<pre><b style="background: blue;padding: 1px 5px;">' . $int . '</b> ';
        print_r($var);
        echo '</pre>';
        $int++;
    }

    public static function varDump($data, $label = '', $return = false)
    {

        $debug = debug_backtrace();
        $callingFile = $debug[0]['file'];
        $callingFileLine = $debug[0]['line'];

        ob_start();
        var_dump($data);
        $c = ob_get_contents();
        ob_end_clean();

        $c = preg_replace("/\r\n|\r/", "\n", $c);
        $c = str_replace("]=>\n", '] = ', $c);
        $c = preg_replace('/= {2,}/', '= ', $c);
        $c = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $c);
        $c = preg_replace('/  /', "    ", $c);
        $c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
        $c = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);

// Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
        $c = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
        $c = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $c);
        $c = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $c);
        $c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);

        $regex = [
// Numberrs
            'numbers' => ['/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i', '$1$2(<span class="number">$3</span>)'],
// Keywords
            'null' => ['/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'],
            'bool' => ['/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'],
// Types
            'types' => ['/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'],
// Objects
            'object' => ['/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'],
// Function
            'function' => ['/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('],
        ];

        foreach ($regex as $x) {
            $c = preg_replace($x[0], $x[1], $c);
        }

        $style = '
                /* outside div - it will float and match the screen */
                .dumpr {
                margin: 2px;
                padding: 2px;
                background-color: #fbfbfb;
                float: left;
                clear: both;
                }
                /* font size and family */
                .dumpr pre {
                color: #000000;
                font-size: 9pt;
                font-family: "Courier New",Courier,Monaco,monospace;
                margin: 0px;
                padding-top: 5px;
                padding-bottom: 7px;
                padding-left: 9px;
                padding-right: 9px;
                }
                /* inside div */
                .dumpr div {
                background-color: #fcfcfc;
                border: 1px solid #d9d9d9;
                float: left;
                clear: both;
                }
                /* syntax highlighting */
                .dumpr span.string {color: #c40000;}
                .dumpr span.number {color: #ff0000;}
                .dumpr span.keyword {color: #007200;}
                .dumpr span.function {color: #0000c4;}
                .dumpr span.object {color: #ac00ac;}
                .dumpr span.type {color: #0072c4;}
                ';

        $style = preg_replace("/ {2,}/", "", $style);
        $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
        $style = preg_replace("/\/\*.*?\*\//i", '', $style);
        $style = str_replace('}', '} ', $style);
        $style = str_replace(' {', '{', $style);
        $style = trim($style);

        $c = trim($c);
        $c = preg_replace("/\n<\/span>/", "</span>\n", $c);

        if ($label == '') {
            $line1 = '';
        } else {
            $line1 = "<strong>$label</strong> \n";
        }

        $out = "\n<!-- Dumpr Begin -->\n" .
            "<style type=\"text/css\">" . $style . "</style>\n" .
            "<div class=\"dumpr\">
<div><pre>$line1 $callingFile : $callingFileLine \n$c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>" .
            "\n<!-- Dumpr End -->\n";
        if ($return) {
            return $out;
        } else {
            echo $out;
        }
    }

}
