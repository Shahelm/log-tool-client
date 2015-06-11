<?php
return array(
    'version'            => 0.91,
    'host'               => 'http://log-tool.carid.com/',
    'time-out'           => 60, //The time interval over which checks new errors.
    'min-time-out'       => 60,
    'number-of-errors'   => 200,
    'lifetime-popup'     => 2000,
    'lifetime-popup-min' => 500,
    'lifetime-popup-max' => 5000,
    'temp-file-name'    => 'log-tool-temp.txt',
    'api-routes'       => array(
        'get-max-time'                  => 'api/time-last-error',
        'check-appearance-of-new-error' => 'api/check-new-error/{timestamp}',
        'number-of-errors'              => 'api/number-of-errors/{timeInterval}', //(PT1M | PT5M)
        'client-latest-version'         => 'api/get-client-latest-version',
        'last-phar-client'              => 'api/get-last-phar-client'
    )
);
