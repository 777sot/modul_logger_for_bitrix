<?php
namespace App\DebugLogger;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Application;

class DataTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return "main_logger";
    }
    public static function getConnectionName()
    {
        return "default";
    }

    // метод возвращающий структуру ORM-сущности
    public static function getMap()
    {
        /*
         * Типы полей:
         * DatetimeField - дата и время
         * DateField - дата
         * BooleanField - логическое поле да/нет
         * IntegerField - числовой формат
         * FloatField - числовой дробный формат
         * EnumField - список, можно передавать только заданные значения
         * TextField - text
         * StringField - varchar
         */
        return array(
            // ID
            new Entity\IntegerField(
            // имя сущности
                "ID",
                array(
                    // первичный ключ
                    "primary" => true,
                    // AUTO INCREMENT
                    "autocomplete" => true,
                )
            ),
            // активность
            new Entity\BooleanField(
                'ACTIVE',
                array(
                    "values" => array('N', 'Y')
                )
            ),
            // cайты
            new Entity\StringField(
            // имя сущности
                "SITE",
                array(
                    // имя колонки в таблице
                    "column_name" => "SITE",
                    // обязательное поле
                    "required" => true,
                )
            ),
            // ссылка перехода
            new Entity\StringField(
            // имя сущности
                "FILE_LOGGER_NAME",
                array(
                    // имя колонки в таблице
                    "column_name" => "FILE_LOGGER_NAME",
                    // обязательное поле
                    "required" => true,
                )
            ),
            // ссылка на картинку
            new Entity\StringField(
            // имя сущности
                "DIR_LOGGER",
                array(
                    // имя колонки в таблице
                    "column_name" => "DIR_LOGGER",
                    // обязательное поле
                    "required" => true,
                )
            ),
        );
    }

    // очистка тегированного кеша при добавлении
    public static function onAfterAdd(Entity\Event $event)
    {
        DataTable::clearCache();
    }
    // очистка тегированного кеша при изменении
    public static function onAfterUpdate(Entity\Event $event)
    {
        DataTable::clearCache();
    }
    // очистка тегированного кеша при удалении
    public static function onAfterDelete(Entity\Event $event)
    {
        DataTable::clearCache();
    }
    // основной метод очистки кеша по тегу
    public static function clearCache()
    {
        // служба пометки кеша тегами
        $taggedCache = Application::getInstance()->getTaggedCache();
        $taggedCache->clearByTag('logger');
    }
}