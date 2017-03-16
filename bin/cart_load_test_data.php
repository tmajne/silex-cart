#!/usr/bin/env php
<?php

use Gog\Entity\Game;

include_once __DIR__."/../vendor/autoload.php";

$app = require __DIR__."/../src/bootstrap.php";

$gameRepository = $app['service.cart']['game.repository'];

$game1 = new Game("Fallout", 199);
$game2 = new Game("Don’t Starve", 299);
$game3 = new Game("Baldur’s Gate", 399);
$game4 = new Game("Icewind Dale", 499);
$game5 = new Game("Bloodborne", 599);

echo "START\n";
echo "Add: {$game1->getTitle()}\n";
$gameRepository->add($game1);
echo "Add: {$game2->getTitle()}\n";
$gameRepository->add($game2);
echo "Add: {$game3->getTitle()}\n";
$gameRepository->add($game3);
echo "Add: {$game4->getTitle()}\n";
$gameRepository->add($game4);
echo "Add: {$game5->getTitle()}\n";
$gameRepository->add($game5);
echo "FINITO\n";