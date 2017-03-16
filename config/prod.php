<?php

declare(strict_types = 1);

use Symfony\Component\HttpFoundation\Request;

$app['cart.config.items.limit'] = 3;
$app['cart.storage.path'] = sys_get_temp_dir().'/prod';

$app->error(function (\Exception $e, Request $request, $code) use ($app) {

    $response = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'code' => $code,
        'data' => null
    ];

    return $app->json($response);
});