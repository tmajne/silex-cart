<?php

declare(strict_types = 1);

namespace App\Provider\Route;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class AppRouteProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/', function () {
            return 'Home page';
        })->bind('home');


        // EXAMPLES
        
        /*$app->get('/exception', 'App\\Http\\Controller\\AppController::json')
            ->bind('exception');*/

        /*$app->get('/hello/{name}', function ($name) use ($app) {
                return 'Hello '.$app->escape($name);
            });*/

        return $controller;
    }
}
