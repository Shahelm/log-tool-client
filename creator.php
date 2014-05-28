<?php
require_once 'app/bootstrap.php';

$phar = new Phar('log-tool-client.phar');

$phar->buildFromDirectory('/var/www/log-tool-client/');

$phar->setStub($phar->createDefaultStub('app/app.php'));