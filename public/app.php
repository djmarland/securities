<?php
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../app/autoload.php';

$env = 'prod';
$debugMode = false;
if (true) { // @todo - detect from environment
    $env = 'dev';
    $debugMode = true;
    \Symfony\Component\Debug\Debug::enable();
}


require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel($env, $debugMode);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);