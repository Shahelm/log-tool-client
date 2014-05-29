<?php
return array(
    'host'              => 'http://log-tool.carid.com/',
    'time-out'          => 60, // Интервал времени через которые производится проверка новых ошибок
    'min-time-out'      => 60,
    'number-of-errors'  => 200,
    'temp-file-name'    => 'log-tool-temp.txt',
    'api-routes'       => array(
        'get-max-time'                  => 'api/time-last-error',
        'check-appearance-of-new-error' => 'api/check-new-error/{timestamp}',
        'number-of-errors'              => 'api/number-of-errors/{timeInterval}' //(PT1M | PT5M) 
    )
);



