<?php

declare(strict_types = 1);

namespace App\Provider;

use Gog\Dao\FastCache\CartDao;
use Gog\Dao\FastCache\GameDao;
use Gog\Repository\CartRepository;
use Gog\Repository\GameRepository;
use phpFastCache\CacheManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CartServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['service.cart'] = function (Container $app) {

            $dic = new Container();

            $dic['cart.repository'] = function($c) {
                return new CartRepository($c['cart.dao']);
            };

            $dic['cart.dao'] = function($c) {
                return new CartDao($c['fastcache']);
            };

            $dic['game.repository'] = function($c) {
                return new GameRepository($c['game.dao']);
            };

            $dic['game.dao'] = function($c) {
                return new GameDao($c['fastcache']);
            };

            $dic['fastcache'] = function($c) {
                CacheManager::setDefaultConfig([
                    'path' => $c['fastcache.path'],
                    'ignoreSymfonyNotice' => true
                ]);
                return CacheManager::getInstance('sqlite');
            };

            $dic['fastcache.path'] = function($c) use ($app) {
                return $app['cart.storage.path'];
            };

            return $dic;
        };
    }
}