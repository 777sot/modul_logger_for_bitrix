# modul_logger_for_bitrix
planning module for bitrix24 box
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
