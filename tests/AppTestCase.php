<?php

declare(strict_types = 1);

namespace App\Test;

use Silex\Application;
use Silex\WebTestCase;
use Mockery;

abstract class AppTestCase extends WebTestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function createApplication() : Application
    {
        $app = require __DIR__."/../src/bootstrap.php";

        // tt
        // configuration app for test
        $app['test'] = true;

        return $app;
    }
}
