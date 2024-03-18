<?php

use Bitrix\Main\Loader;

//ini_set("memory_limit", "1512M");
//ini_set('MAX_EXECUTION_TIME', '-1');
//set_time_limit('3000');

if (Loader::includeModule('main.logger')) {

    Loader::registerAutoLoadClasses(
        'main.logger',
        [
            'App\\DebugLogger\\DebugLogger' => 'lib/DebugLogger/DebugLogger.php',
            'App\\DebugLogger\\DebugLoggerException' => 'lib/DebugLogger/DebugLoggerException.php',
            'App\\DebugLogger\\DebugLoggerInterface' => 'lib/DebugLogger/DebugLoggerInterface.php',
            'App\\DebugLogger\\Logger' => 'lib/Logger.php',
            'App\\DebugLogger\\DataTable' => 'lib/Data.php',
        ]
    );
}

?>