<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use \Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use App\DebugLogger\DataTable;

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("main.logger");
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
// вывод заголовка
$APPLICATION->SetTitle("Настройки модуля Logger");
// подключаем языковые файлы
IncludeModuleLangFile(__FILE__);
$aTabs = [
    [
        "DIV" => "edit1",
        // название вкладки в табах
        "TAB" => "Параметры",
        // заголовок и всплывающее сообщение вкладки
        "TITLE" => "Параметры логирования",
    ],
    [
        "DIV" => "edit2",
        "TAB" => "Как использовать",
        "TITLE" => "Примеры логирования",
    ],

];

// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

Loader::includeModule("main.logger");

if (
    // проверка метода вызова страницы
    $REQUEST_METHOD == "POST"
    &&
    // проверка нажатия кнопок Сохранить
    $save != ""
    &&
    // проверка наличия прав на запись для модуля
    $POST_RIGHT == "W"
    &&
    // проверка идентификатора сессии
    check_bitrix_sessid()
) {
    // класс таблицы в базе данных
    $bookTable = new DataTable;
    // обработка данных формы

    $arFields = [
        "ACTIVE" => ($ACTIVE == '') ? 'N' : 'Y',
        "SITE" => htmlspecialchars((string)$_REQUEST['SITE']),
        "FILE_LOGGER_NAME" => htmlspecialchars((string)$_REQUEST['FILE_LOGGER_NAME']),
        "DIR_LOGGER" => htmlspecialchars((string)$_REQUEST['DIR_LOGGER']),
    ];
    // обновляем запись
    $res = $bookTable->Update(1, $arFields);
    // если обновление прошло успешно
    if ($res->isSuccess()) {
        // перенаправим на новую страницу, в целях защиты от повторной отправки формы нажатием кнопки Обновить в браузере
        if ($save != "") {
            // если была нажата кнопка Сохранить, отправляем обратно на форму
            LocalRedirect("/bitrix/admin/logger_admin.php?mess=ok&lang=" . LANG);
        }
    }
    // если обновление прошло не успешно
    if (!$res->isSuccess()) {
        // если в процессе сохранения возникли ошибки - получаем текст ошибки
        if ($e = $APPLICATION->GetException())
            $message = new CAdminMessage("Ошибка сохранения: ", $e);
        else {
            $mess = print_r($res->getErrorMessages(), true);
            $message = new CAdminMessage("Ошибка сохранения: " . $mess);
        }
    }
}
// подготовка данных для формы, полученные из БД данные будем сохранять в переменные с префиксом str_
$result = new DataTable();

if ($result::getRowById(1)) {
    $bookTable = $result::getRowById(1);
    $str_ACTIVE = $bookTable["ACTIVE"];
    $str_SITE = $bookTable["SITE"];
    $str_FILE_LOGGER_NAME = $bookTable["FILE_LOGGER_NAME"];
    $str_DIR_LOGGER = $bookTable["DIR_LOGGER"];
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
// eсли есть сообщения об успешном сохранении, выведем их
if ($_REQUEST["mess"] == "ok") {
    CAdminMessage::ShowMessage(["MESSAGE" => "Сохранено успешно", "TYPE" => "OK"]);
}
// eсли есть сообщения об не успешном сохранении, выведем их
if ($message) {
    echo $message->Show();
}
// eсли есть сообщения об не успешном сохранении от ORM, выведем их
if ($bookTable->LAST_ERROR != "") {
    CAdminMessage::ShowMessage($bookTable->LAST_ERROR);
}
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
<?

echo bitrix_sessid_post();

$tabControl->Begin();
$tabControl->BeginNextTab();
?>
    <tr>
        <td width="40%"><?= "Активность" ?></td>
        <td width="60%"><input type="checkbox" name="ACTIVE" value="Y" <? if ($str_ACTIVE == "Y") echo " checked" ?>>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <label for="SITE"><?= "Сайты" ?></label>
        </td>
        <td width="60%">
            <select name="SITE" multiple>
                <?php
                if (Loader::includeModule('main')) {
                    $sites = SiteTable::getList([
                        'select' => ['LID', 'NAME'],
                    ]);
                    while ($site = $sites->fetch()) {
                        echo '<option value="' . $site["LID"] . '"';
                        echo $site["LID"] === $str_SITE ? 'selected' : '';
                        echo '>' . $site["NAME"] . '</option>';
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><?= "Имя файла ( <span style='color:red;'>пример :</span> debug_logger)" ?></td>
        <td width="60%"><input type="text" name="FILE_LOGGER_NAME" value="<?php echo $str_FILE_LOGGER_NAME ?>"/></td>
    </tr>
    <tr>
        <td width="40%"><?= "Путь к файлу логера от корня (<span style='color:red;'>пример :</span> /local/logs/)" ?></td>
        <td width="60%"><input type="text" name="DIR_LOGGER" value="<?php echo $str_DIR_LOGGER ?>"/></td>
    </tr>
    <tr>
        <td>
            <input class="adm-btn-save" type="submit" name="save" value="Сохранить настройки"/>
        </td>
    </tr>

<?
//$tabControl->Buttons();
$tabControl->BeginNextTab();
?>
    <tr>
        <td>
            <div>
                \Bitrix\Main\Loader::includeModule("main.logger"); <br><br>

                $logger = new App\DebugLogger\Logger();<br><br>
                <pre>
           $arFields = [ <br>
                "ACTIVE" => 'Y',<br>
                "SITE" => 's1',<br>
                "FILE_LOGGER_NAME" => 'Logger',<br>
                "DIR_LOGGER" => '/local/log/',<br>
            ]<br>
        $logger->save($arFields, __METHOD__, __LINE__);<br>
                    <h3 style="color:green;">РЕЗУЛЬТАТ ЛОГИРОВАНИЯ :</h3><br>
        *** hmevug9 [2024-03-17 22:27:10.064451 +03:00 Δ- s, 19.92/22.00 MiB] ********************
        *  13
        {
            "ACTIVE": "Y",
            "SITE": "s1",
            "FILE_LOGGER_NAME": "Logger",
            "DIR_LOGGER": "\/local\/log\/"
        }
            </pre>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <h3 style="color:green;">Если прописать в файле init.php : </h3>
            <pre>
                use App\DebugLogger\Logger;

                if (Bitrix\Main\Loader::includeModule('main.logger')){
                    $logger = new Logger();
                }
            </pre>

            <h3 style="color:green;">то можете использовать в любом месте просто вызывая метод </h3>
            $logger->save($arFields, __METHOD__, __LINE__);<br>
            <h3 style="color:red;">После удаления модуля удалите данные из файла init.php !!!</h3>
        </td>
    </tr>

<?php
$tabControl->End();
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
