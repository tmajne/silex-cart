<?php

declare(strict_types = 1);

namespace App\Provider\Route;

use App\Http\Controller\GameController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class GameRouteProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $app['game.controller'] = function($app) {
            $cartContainer =  $app['service.cart'];

            return new GameController($app, $cartContainer['game.repository']);
        };

        $controller = $app['controllers_factory'];

        $controller->get('', 'game.controller:index')
            ->bind('game.index');

        $controller->get('{gameId}', 'game.controller:show')
            ->bind('game.show');

        $controller->put('', 'game.controller:create')
            ->bind('game.create');

        $controller->post('{gameId}', 'game.controller:update')
            ->bind('game.update');

        $controller->delete('{gameId}', 'game.controller:remove')
            ->bind('game.remove');

        $controller->put('load/test/data', 'game.controller:loadTestData')
            ->bind('game.load.data');

        return $controller;
    }
}
