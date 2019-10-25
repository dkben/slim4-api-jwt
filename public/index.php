<?php

use Entity\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';


$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/create', function (Request $request, Response $response, $args) use ($entityManager) {
    $product = new Product();
    $product->setName('ben');
    $entityManager->persist($product);
    $entityManager->flush();

    $response->getBody()->write("Create! ID: " . $product->getId());
    return $response;
});

$app->get('/read', function (Request $request, Response $response, $args) use ($entityManager) {
    $productRepository = $entityManager->getRepository(Product::class);
    $products = $productRepository->getById(3);
    $product = $products[0];
    $response->getBody()->write("Read! ID: " . $product->getName());
    return $response;
});

$app->run();