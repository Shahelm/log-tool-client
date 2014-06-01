<?php
require_once 'bootstrap.php';

use Lib\HttpHelper;
use Lib\Storage\StorageHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

$app = new Application();

$helperSet = new HelperSet(array(
    'httpClient'    => new HttpHelper(),
    'storageHelper' => new StorageHelper(),
));

$app->setHelperSet($helperSet);

$app->add(new \Commands\StartCommand());

$app->add(new \Commands\StopCommand());

$app->run();
