<?php
namespace Raptor\Application\Webserver;

use Pimple\Container;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Http\Response;
use FastRoute\Dispatcher;
use React\Http\Server;
use function \FastRoute\simpleDispatcher;

class Factory
{
    public function buildServer(Container $container, array $config) : Server
    {
        $dispatcher = simpleDispatcher(function(RouteCollector $routes) use ($container, $config) {
            foreach ($config as $routeConfig) {
                $methods = $routeConfig['method'] ?? ['GET'];
                $controller = $container->offsetGet($routeConfig['controller']);
                $routes->addRoute($methods, $routeConfig['route'], [$controller, $routeConfig['action']]);
            }
        });

        return new Server(
            function (Request $request) use ($dispatcher) {
                $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

                switch ($routeInfo[0]) {
                    case Dispatcher::NOT_FOUND:
                        return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
                    case Dispatcher::METHOD_NOT_ALLOWED:
                        return new Response(405, ['Content-Type' => 'text/plain'], 'Method not allowed');
                    case Dispatcher::FOUND:
                        return $routeInfo[1]($request, ... array_values($routeInfo[2]));
                }
            }
        );
    }
}