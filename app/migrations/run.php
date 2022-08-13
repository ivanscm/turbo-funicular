<?php

use Nextras\Migrations\Bridges;
use Nextras\Migrations\Controllers;
use Nextras\Migrations\Drivers;
use Nextras\Migrations\Extensions;

require __DIR__ . '/../vendor/autoload.php';

$configurator = App\Bootstrap::boot();
$container = $configurator->createContainer();

$connection = $container->getService('database.default.connection');

$db = new Bridges\NetteDatabase\NetteAdapter($connection);

$driver = new Drivers\PgSqlDriver($db);

$controller = new Controllers\ConsoleController($driver);

$baseDir = __DIR__;
$controller->addGroup('structures', "$baseDir/structures");
$controller->addGroup('basic-data', "$baseDir/basic-data", ['structures']);
$controller->addGroup('dummy-data', "$baseDir/dummy-data", ['basic-data']);
$controller->addExtension('sql', new Extensions\SqlHandler($driver));

$controller->run();