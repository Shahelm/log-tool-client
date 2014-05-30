<?php
return array(
    'host'              => 'http://log-tool.carid.com/',
    'time-out'          => 60, //The time interval over which checks new errors.
    'min-time-out'      => 60,
    'number-of-errors'  => 200,
    'lifetime-popup'    => 2000,
    'temp-file-name'    => 'log-tool-temp.txt',
    'api-routes'       => array(
        'get-max-time'                  => 'api/time-last-error',
        'check-appearance-of-new-error' => 'api/check-new-error/{timestamp}',
        'number-of-errors'              => 'api/number-of-errors/{timeInterval}' //(PT1M | PT5M) 
    )
);



