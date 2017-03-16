<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Silex\Application;

abstract class AppController
{
    /** @var Application  */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function restSuccessResponse(array $data = null)
    {
        $response = [
            'status' => 'success',
            'data' => $data
        ];

        return $this->app->json($response, 200);
    }

    protected function restFailResponse(?array $data = null)
    {
        $response = [
            'status' => 'fail',
            'data' => $data
        ];

        return $this->app->json($response, 200);
    }

    protected function restNotFoundResponse(string $message, int $code = null, array $data = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'data' => $data
        ];

        return $this->app->json($response, 404);
    }

    protected function restErrorResponse(string $message, int $code = null, array $data = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'data' => $data
        ];

        return $this->app->json($response, 500);
    }
}
