<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Gog\Contract\EntityInterface;
use Gog\Contract\Repository\CartRepositoryInterface;
use Gog\Contract\Repository\GameRepositoryInterface;
use Gog\Entity\Cart;
use Gog\Exception\CartItemNotFoundException;
use Gog\Exception\EntityNotFoundException;
use Gog\Repository\CartRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AppController
{
    /** @var CartRepository */
    private $cartRepository;

    /** @var GameRepository */
    private $gameRepository;

    /**
     * CartController constructor.
     *
     * @param Application             $app
     * @param CartRepositoryInterface $cartRepository
     * @param GameRepositoryInterface $gameRepository
     */
    public function __construct(
        Application $app,
        CartRepositoryInterface $cartRepository,
        GameRepositoryInterface $gameRepository
    ) {
        $this->app = $app;
        $this->cartRepository = $cartRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 3);

        $carts = $this->cartRepository->getAll($page, $limit);

        $response = [];
        foreach ($carts as $cart) {
            $response[] = $cart->toArray();
        }

        return $this->restSuccessResponse($response);
    }

    /**
     * @param mixed $cartId
     * @return JsonResponse
     */
    public function show($cartId)
    {
        try {
            /** @var EntityInterface $game */
            $cart = $this->cartRepository->get($cartId);
            return $this->restSuccessResponse($cart->toArray());
        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return JsonResponse
     */
    public function create()
    {
        $cart = new Cart();
        if (array_key_exists('cart.config.items.limit', $this->app)) {
            $itemsLimit = $this->app['cart.config.items.limit'];
            $cart->setItemsLimit($itemsLimit);
        }

        $this->cartRepository->add($cart);

        return $this->restSuccessResponse();
    }

    /**
     * @param mixed $cartId
     * @return JsonResponse
     */
    public function remove($cartId)
    {
        try {
            $cart = $this->cartRepository->get($cartId);
            $this->cartRepository->remove($cart);
            return $this->restSuccessResponse();
        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param mixed $cartId
     * @param mixed $productId
     * @return JsonResponse
     */
    public function addItem(Request $request, $cartId, $productId)
    {
        try {
            $count = (int) $request->query->get('count', 1);

            /** @var Game $game */
            $product = $this->gameRepository->get($productId);

            /** @var Cart $cart */
            $cart = $this->cartRepository->get($cartId);

            $cart->addItem($product, $count);
            $this->cartRepository->add($cart);

            return $this->restSuccessResponse();
        } catch (EntityNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param mixed $cartId
     * @param mixed $productId
     * @return JsonResponse
     */
    public function removeItem($cartId, $productId)
    {
        try {
            /** @var Game $game */
            $game = $this->gameRepository->get($productId);

            /** @var Cart $cart */
            $cart = $this->cartRepository->get($cartId);

            $cart->removeItem($game);
            $this->cartRepository->add($cart);

            return $this->restSuccessResponse();
        } catch (EntityNotFoundException|CartItemNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage(), $e->getCode());
        }
    }

    public function changeItemQuantity($cartId, $productId, int $deltaQuantity)
    {
        try {
            /** @var Game $game */
            $game = $this->gameRepository->get($productId);

            /** @var Cart $cart */
            $cart = $this->cartRepository->get($cartId);

            $cart->changeItemQuantity($game, $deltaQuantity);
            $this->cartRepository->add($cart);

            return $this->restSuccessResponse();
        } catch (EntityNotFoundException|CartItemNotFoundException $e) {
            return $this->restNotFoundResponse($e->getMessage(), $e->getCode());
        }
    }
}
