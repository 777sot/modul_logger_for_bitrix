<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

// определяем в какой папке находится модуль, если в bitrix, инклудим файл с меню из папки bitrix
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main.logger/")) {

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main.logger/admin/logger_admin.php");
}
// определяем в какой папке находится модуль, если в local, инклудим файл с меню из папки local
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/modules/main.logger/")) {
   
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/main.logger/admin/logger_admin.php");
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';