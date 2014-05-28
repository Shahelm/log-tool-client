<?php 
return array(
    // Интервал времени через которые производится проверка новых ошибок
    'time-out' => 300,
    'api-routes' => array(
        'get-max-time'     => '/api/time-last-error',
        'get-error-count'  => '/api/error-count/{timestamp}/json',
        'get-error-on-web' => '/api/error-on-web/{timestamp}/json',
        'info-last-error'  => '/api/info-last-error/{timestamp}/json',
        //(PT1M | PT5M) 
        'number-of-errors' => '/api/number-of-errors/{time-interval}/json'
    )
);



