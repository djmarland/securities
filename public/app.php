<?php
use Symfony\Component\HttpFoundation\Request;

header_remove('X-Powered-By');
date_default_timezone_set('Europe/London');

require __DIR__.'/../app/autoload.php';

// safe default settings
$env = 'prod';
$debugMode = false;

// get env from hostname
$hostname = $_SERVER['HTTP_HOST'] ?? null;

$environments = [
    'isin.app' => 'dev',
    'beta.isinanalytics.com' => 'beta',
    'alpha.isinanalytics.com' => 'alpha',
    'www.isinanalytics.com' => 'prod',
];

$env = $environments[$hostname] ?? $env;

if ('dev' == $env) {
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
