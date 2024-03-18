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

}