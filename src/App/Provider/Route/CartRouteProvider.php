<?php

declare(strict_types = 1);

namespace App\Provider\Route;

use App\Http\Controller\CartController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class CartRouteProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $app['cart.controller'] = function ($app) {
            $cartContainer =  $app['service.cart'];

            return new CartController(
                $app,
                $cartContainer['cart.repository'],
                $cartContainer['game.repository']
            );
        };

        $controller = $app['controllers_factory'];

        $controller->get('admin', 'cart.controller:index')
            ->bind('cart.admin.index');

        $controller->get('{cartId}', 'cart.controller:show')
            ->bind('cart.show');

        $controller->put('', 'cart.controller:create')
            ->bind('cart.create');

        $controller->delete('{cartId}', 'cart.controller:remove')
            ->bind('cart.remove');


        $controller->put('{cartId}/items/{productId}', 'cart.controller:addItem')
            ->bind('cart.item.add');

        $controller->post('{cartId}/items/{productId}/{deltaQuantity}', 'cart.controller:changeItemQuantity')
            ->bind('cart.item.changeQuantity');

        $controller->delete('{cartId}/items/{productId}', 'cart.controller:removeItem')
            ->bind('cart.item.remove');

        return $controller;
    }
}
