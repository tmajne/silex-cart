<?php

declare(strict_types = 1);

use App\Provider\CartServiceProvider;
use App\Provider\Route\AppRouteProvider;
use App\Provider\Route\CartRouteProvider;
use App\Provider\Route\GameRouteProvider;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

$app = new Application();

require __DIR__."/../config/prod.php";

// PROVIDERS
// ...

if (getenv('APP_ENV') === "development") {
    require __DIR__."/../config/dev.php";
    require __DIR__."/dev.php";
} elseif (getenv('APP_ENV') === "testing") {
    require __DIR__."/../config/test.php";
}

// ROUTES

$app->register(new ServiceControllerServiceProvider());
$app->mount('', new AppRouteProvider());
$app->mount('games', new GameRouteProvider());
$app->mount('carts', new CartRouteProvider());

// PROVIDERS

$app->register(new CartServiceProvider());


return $app;
