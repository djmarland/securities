<?php
use Symfony\Component\HttpFoundation\Request;

header_remove('X-Powered-By');
date_default_timezone_set('Europe/London');

require __DIR__ . '/../app/autoload.php';

// safe default settings
$env = 'prod';
$debugMode = false;

// override if we can
$serverEnv = getenv('APP_ENV');

if ($serverEnv && in_array($serverEnv, ['dev', 'prod'])) {
    $env = $serverEnv;
}

if ('dev' == $env) {
    $env = 'dev';
    $debugMode = true;
    \Symfony\Component\Debug\Debug::enable();
}


require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel($env, $debugMode);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
