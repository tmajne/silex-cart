<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Gog\Contract\EntityInterface;
use Gog\Contract\Repository\GameRepositoryInterface;
use Gog\Entity\Game;
use Gog\Exception\EntityNotFoundException;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class GameController extends AppController
{
    /** @var GameRepository */
    private $gameRepository;

    public function __construct(
        Application $app,
        GameRepositoryInterface $gameRepository
    ) {
        $this->app = $app;
        $this->gameRepository = $gameRepository;
    }

    public function index(Request $request)
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 3);

        $games = $this->gameRepository->getAll($page, $limit);

        $response = [];
        foreach ($games as $game) {
            $response[] = $game->toArray();
        }

        return $this->restSuccessResponse($response);
    }

    public function show($gameId)
    {
        try {
            /** @var EntityInterface $game */
            $game = $this->gameRepository->get($gameId);
            return $this->restSuccessResponse($game->toArray());
        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $title = $request->request->get('title');
            $price = (int) $request->request->get('price');

            if (!$title) {
                throw new \InvalidArgumentException('Title is require');
            }
            if (!$price) {
                throw new \InvalidArgumentException('Price is require');
            }

            $game = new Game($title, $price);
            $this->gameRepository->add($game);

            return $this->restSuccessResponse();
        } catch (\InvalidArgumentException $e) {
            return $this->restFailResponse([$e->getMessage()]);
        }
    }

    public function remove($gameId)
    {
        try {
            /** @var EntityInterface $game */
            $game = $this->gameRepository->get($gameId);
            $this->gameRepository->remove($game);
            return $this->restSuccessResponse();
        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage());
        }
    }

    public function update($gameId, Request $request)
    {
        try {
            $title = $request->request->get('title');
            $price = (int) $request->request->get('price');

            $game = $this->gameRepository->get($gameId);

            $title && $game->setTitle($title);
            $price && $game->setPrice($price);

            $this->gameRepository->add($game);

            return $this->restSuccessResponse();

        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage());
        }
    }

    public function loadTestData()
    {
        $game1 = new Game("Fallout", 199);
        $game2 = new Game("Don’t Starve", 299);
        $game3 = new Game("Baldur’s Gate", 399);
        $game4 = new Game("Icewind Dale", 499);
        $game5 = new Game("Bloodborne", 599);

        $this->gameRepository->add($game1);
        $this->gameRepository->add($game2);
        $this->gameRepository->add($game3);
        $this->gameRepository->add($game4);
        $this->gameRepository->add($game5);

        return $this->restSuccessResponse();
    }
}
