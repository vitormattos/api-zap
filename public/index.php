<?php

use Api\Route;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
Route::build($app);

$app->run();
