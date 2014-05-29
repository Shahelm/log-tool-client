<?php
require_once 'bootstrap.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$app = new Application();

$helperSet = new HelperSet(array(
    'httpClient'    => new \Lib\HttpHelper(),
));

$app->setHelperSet($helperSet);

$app->add(new \Commands\StartCommand());

$app->add(new \Commands\StopCommand());

$app->run();
