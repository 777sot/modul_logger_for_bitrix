<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
// собираем зарегистрированные через RegisterModuleDependences и AddEventHandler обработчики события OnSomeEvent
$rsHandlers = GetModuleEvents("main.logger", "OnSomeEvent");
// перебираем зарегистрированные в системы события
while ($arHandler = $rsHandlers->Fetch()) {
    // выполняем каждое зарегистрированное событие по одному
    ExecuteModuleEventEx($arHandler, array(/* параметры которые нужно передать в модуль */));
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';