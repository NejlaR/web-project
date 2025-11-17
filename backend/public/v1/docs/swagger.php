<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([
    realpath(__DIR__ . '/doc_setup.php'),
    realpath(__DIR__ . '/../../../rest/routes')
]);

header('Content-Type: application/json');
echo $openapi->toJson();
