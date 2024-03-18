# modul_logger_for_bitrix
planning module for bitrix24 box

                \Bitrix\Main\Loader::includeModule("main.logger");

                $logger = new App\DebugLogger\Logger();                
              
           $arFields = [ 
                "ACTIVE" => 'Y',
                "SITE" => 's1',
                "FILE_LOGGER_NAME" => 'Logger',
                "DIR_LOGGER" => '/local/log/',
            ]
            
        $logger->save($arFields, __METHOD__, __LINE__);
        
                   РЕЗУЛЬТАТ ЛОГИРОВАНИЯ :
                    
        *** hmevug9 [2024-03-17 22:27:10.064451 +03:00 Δ- s, 19.92/22.00 MiB] ********************
        *  13
        {
            "ACTIVE": "Y",
            "SITE": "s1",
            "FILE_LOGGER_NAME": "Logger",
            "DIR_LOGGER": "\/local\/log\/"
        }        

           Если прописать в файле init.php : 
          
                use App\DebugLogger\Logger;

                if (Bitrix\Main\Loader::includeModule('main.logger')){
                    $logger = new Logger();
                }
          

            то можете использовать в любом месте просто вызывая метод 
            
            $logger->save($arFields, __METHOD__, __LINE__);
            
            После удаления модуля удалите данные из файла init.php !!!

