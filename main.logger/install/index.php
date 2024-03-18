<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

Class main_logger extends CModule
{
    public $errors;

    public function __construct()
    {
        if (file_exists(__DIR__ . "/version.php")) {

            $arModuleVersion = [];

            include_once(__DIR__ . "/version.php");

            $this->MODULE_ID = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("MAIN_LOGGER_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("MAIN_LOGGER_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("MAIN_LOGGER_PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("MAIN_LOGGER_PARTNER_URI");

        }

    }

    public function DoInstall()
    {
        global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {

            ModuleManager::registerModule($this->MODULE_ID);

            if (!IsModuleInstalled("alexey.mycar")) {
                $this->InstallDB();
                $this->InstallEvents();
                $this->InstallFiles();
            }


        } else {

            $APPLICATION->ThrowException(
                Loc::getMessage("MAIN_LOGGER_INSTALL_ERROR_VERSION")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("MAIN_LOGGER_INSTALL_TITLE") . " \"" . Loc::getMessage("MAIN_LOGGER_NAME") . "\"",
            __DIR__ . "/step.php"
        );

        return false;

    }

    public function DoUninstall()
    {

        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("MAIN_LOGGER_UNINSTALL_TITLE") . " \"" . Loc::getMessage("MAIN_LOGGER_NAME") . "\"",
            __DIR__ . "/unstep.php"
        );

        return false;
    }

    function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/local/components",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );
        CopyDirFiles(
            __DIR__ . "/themes",
            $_SERVER["DOCUMENT_ROOT"] . "/local/themes",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/",
            true,
            true
        );
        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/" . $this->MODULE_ID)) {
            // удаляет папку из указанной директории, функция работает рекурсивно
            DeleteDirFilesEx(
                "/bitrix/components/" . $this->MODULE_ID
            );
        }
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );
        DeleteDirFiles(
            __DIR__ . "/themes/logger",
            $_SERVER["DOCUMENT_ROOT"] . "/local/themes/logger"
        );
        DeleteDirFilesEx("/local/themes/logger/icons");//icons
        DeleteDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/"
        );
        return true;
    }

    public function InstallDB()
    {
        global $DB;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/main.logger/install/db/install.sql");

        if (!$this->errors) {
            return true;
        }
        return $this->errors;

    }

    public function UnInstallDB()
    {
        global $DB;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/main.logger/install/db/uninstall.sql");

        if (!$this->errors) {
            return true;
        }
        return $this->errors;
    }

    public function InstallEvents()
    {

//        EventManager::getInstance()->registerEventHandler(
//            "main",
//            "OnBeforeEndBufferContent",
//            $this->MODULE_ID,
//            TenPix\TablesKlinikon::class,
//            "updateTables"
//        );
//
//        return false;
        return true;
    }

    public function UnInstallEvents()
    {

//        EventManager::getInstance()->unRegisterEventHandler(
//            "main",
//            "OnBeforeEndBufferContent",
//            $this->MODULE_ID,
//            TenPix\TablesKlinikon::class,
//            "updateTables"
//        );
//
//        return false;
        return true;;
    }

}
