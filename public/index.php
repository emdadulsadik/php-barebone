<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Loader\YamlFileLoader as RoutingYamlLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/src/config'));
$loader->load('services.yaml');
$container->compile();

$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);

$routeLoader = new RoutingYamlLoader(new FileLocator(dirname(__DIR__) . '/src/routes'));
$routes = $routeLoader->load('routes.yaml');

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());

    [$controllerClass, $method] = $parameters['_controller'];

    $controller = $container->get($controllerClass);
    $response = $controller->$method($request, $parameters);

    if (!$response instanceof Response) {
        throw new \RuntimeException('Controller must return a Response object');
    }

} catch (ResourceNotFoundException $e) {
    $response = new Response('404 Not Found', 404);
} catch (\Throwable $e) {
    $response = new Response('500 Internal Server Error: ' . $e->getMessage(), 500);
}

$response->send();
