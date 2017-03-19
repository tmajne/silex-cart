<?php

declare(strict_types = 1);

namespace App\Test\App\Http\Controller;

use App\Test\AppTestCase;
use Nova\Contract\Repository\GameRepositoryInterface;
use Nova\Entity\Game;
use Nova\Exception\EntityNotFoundException;
use Mockery\CountValidator\Exception;

class GameControllerTest extends AppTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->app['service.cart']['game.repository'] = function () {
            return \Mockery::mock(GameRepositoryInterface::class);
        };
    }

    public function testErrorResponse()
    {
        $expectedResponse = '{"status":"error","message":"Example exception","code":500,"data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('getAll')->andThrow(new Exception('Example exception'));

        $client = $this->createClient();
        $client->request('GET', '/games/');
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertFalse($response->isOk());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame($expectedResponse, $content);
    }

    public function testIndex()
    {
        $getAllResult = [
            new Game('Quake 1', 100),
            new Game('Quake 2', 200),
            new Game('Quake 3', 300),
            new Game('Quake 4', 400),
        ];

        $expectedResponse = '{"status":"success","data":[{"id":null,"title":"Quake 1","price":100},{"id":null,'
            .'"title":"Quake 2","price":200},{"id":null,"title":"Quake 3","price":300},{"id":null,"title":"Quake 4",'
            .'"price":400}]}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($getAllResult);

        $client = $this->createClient();
        $client->request('GET', '/games/');
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expectedResponse, $content);
    }

    public function testIndexNoGames()
    {
        $getAllResult = [];
        $expectedResponse = '{"status":"success","data":[]}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($getAllResult);

        $client = $this->createClient();
        $client->request('GET', '/games/');
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expectedResponse, $content);
    }

    public function testShow()
    {
        $testId = 149;
        $getResult = new Game('Quake 1', 100);
        $getResult->setId($testId);

        $expectedResponse = '{"status":"success","data":{"id":149,"title":"Quake 1","price":100}}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andReturn($getResult);

        $client = $this->createClient();
        $client->request('GET', '/games/'.$testId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expectedResponse, $content);
    }

    public function testShowNotFoundEntity()
    {
        $testId = 149;
        $expectedResponse = '{"status":"error","message":"Entity not found: 149","code":null,"data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andThrow(new EntityNotFoundException('Entity not found: '.$testId));

        $client = $this->createClient();
        $client->request('GET', '/games/'.$testId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertFalse($response->isOk());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($expectedResponse, $content);
    }

    public function testCreate()
    {
        $title = 'Queake 1';
        $price = 195;

        $expectedResponse = '{"status":"success","data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('add')
            ->once()
            ->andReturnUsing(function (Game $game) use ($title, $price) {
                $this->assertSame($title, $game->getTitle());
                $this->assertSame($price, $game->getPrice());
            });

        $client = $this->createClient();
        $client->request('PUT', '/games/', ['title' => $title, 'price' => $price]);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expectedResponse, $content);
    }

    /**
     * @dataProvider createInvalidArgumentsDataProvider
     */
    public function testCreateWithInvalidArguments($title, $price, $expected)
    {
        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('add')
            ->never();

        $client = $this->createClient();
        $client->request('PUT', '/games/', ['title' => $title, 'price' => $price]);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expected, $content);
    }

    public function createInvalidArgumentsDataProvider()
    {
        return [
            ['title' => 'Quake', 'price' => null, 'expected' => '{"status":"fail","data":["Price is require"]}'],
            ['title' => null, 'price' => 580, 'expected' => '{"status":"fail","data":["Title is require"]}']
        ];
    }

    public function testRemove()
    {
        $testId = 149;
        $getResult = new Game('Quake 1', 100);
        $getResult->setId($testId);

        $expectedResponse = '{"status":"success","data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andReturn($getResult);
        $gameRepository->shouldReceive('remove')
            ->once()
            ->andReturnUsing(function (Game $game) use ($getResult) {
                $this->assertSame($getResult, $game);
            });

        $client = $this->createClient();
        $client->request('DELETE', '/games/'.$testId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expectedResponse, $content);
    }


    public function testRemoveNotFoundEntity()
    {
        $testId = 149;
        $expectedResponse = '{"status":"error","message":"Entity not found: 149","code":null,"data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andThrow(new EntityNotFoundException('Entity not found: '.$testId));
        $gameRepository->shouldReceive('remove')->never();

        $client = $this->createClient();
        $client->request('DELETE', '/games/'.$testId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertFalse($response->isOk());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($expectedResponse, $content);
    }

    /**
     * @dataProvider updateInvalidArgumentsDataProvider
     */
    public function testUpdate($gameId, $title, $price, $expected)
    {
        $getResult = new Game('Quake 1', 100);
        $getResult->setId($gameId);

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andReturn($getResult);
        $gameRepository->shouldReceive('add')
            ->once()
            ->andReturnUsing(function (Game $game) use ($title, $price, $gameId, $getResult) {
                if ($title) {
                    $this->assertSame($title, $game->getTitle());
                } else {
                    $this->assertSame($getResult->getTitle(), $game->getTitle());
                }
                if ($price) {
                    $this->assertSame($price, $game->getPrice());
                } else {
                    $this->assertSame($getResult->getPrice(), $game->getPrice());
                }
                $this->assertSame($gameId, $game->getId());
            });

        $client = $this->createClient();
        $client->request('POST', '/games/'.$gameId, ['title' => $title, 'price' => $price]);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertSame($expected, $content);
    }

    public function updateInvalidArgumentsDataProvider()
    {
        $expected = '{"status":"success","data":null}';
        return [
            ['gameId' => 243, 'title' => 'Quake Extended', 'price' => 800, 'expected' => $expected],
            ['gameId' => 843, 'title' => null, 'price' => null, 'expected' => $expected],
            ['gameId' => 242, 'title' => null, 'price' => 800, 'expected' => $expected],
            ['gameId' => 2, 'title' => 'Quake Extended', 'price' => null, 'expected' => $expected],
        ];
    }

    public function testUpdateNotFoundEntity()
    {
        $testId = 149;
        $expectedResponse = '{"status":"error","message":"Entity not found: 149","code":null,"data":null}';

        $gameRepository = $this->app['service.cart']['game.repository'];
        $gameRepository->shouldReceive('get')
            ->once()
            ->andThrow(new EntityNotFoundException('Entity not found: '.$testId));
        $gameRepository->shouldReceive('add')->never();

        $client = $this->createClient();
        $client->request('POST', '/games/'.$testId);
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertFalse($response->isOk());
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame($expectedResponse, $content);
    }
}
