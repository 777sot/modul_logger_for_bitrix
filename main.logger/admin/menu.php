<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// сформируем верхний пункт меню
$aMenu = array(
    // пункт меню в разделе Контент
    'parent_menu' => 'global_menu_services',
    // сортировка
    'sort' => 1,
    // название пункта меню
    'text' => "Logger",
    // идентификатор ветви
    "items_id" => "menu_webforms",
    // иконка
    "icon" => "form_menu_icon",
);

// дочерния ветка меню
$aMenu["items"][] =  array(
    // название подпункта меню
    'text' => 'Настройки модуля',
    // ссылка для перехода
    'url' => 'logger_admin.php?lang=ru&mid=main.logger'
);
// возвращаем основной массив $aMenu
return $aMenu;
