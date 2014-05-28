<?php
require_once 'bootstrap.php';

use Symfony\Component\Console\Application;

$app = new Application();

$client = new Guzzle\Http\Client();

/**
 * @var \Guzzle\Http\Message\Request $response
 */
$response = $client->get('http://dev.log-tool.loc/api/time-last-error');

$response->send();

$res = $response->getResponse();
echo "\n";
print_r('<pre>');print_r($res->getHeaderLines());print_r('</pre>');
echo "\n";
print_r('<pre>');print_r($res->getStatusCode());print_r('</pre>');
echo "\n";
print_r('<pre>');print_r($res->getBody(true));print_r('</pre>');
echo "\n";
echo "\n";
//var_export($response->json());

//$app->run();
