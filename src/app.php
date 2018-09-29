<?php
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;
use FastRoute\RouteCollector;

require __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$indexPage = function() {
    return new Response(200, ['Content-Type' => 'text/plain'], "Hello world\n");
};

$greeter = function(ServerRequestInterface $request, $name) {
    return new Response(200, ['Content-Type' => 'text/plain'], "Hello $name\n");
};

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $routes) use ($indexPage, $greeter) {
    $routes->addRoute('GET', '/', $indexPage);
    $routes->addRoute('GET', '/hello/{name:\w+}', $greeter);
});

$server = new Server(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            return new Response(405, ['Content-Type' => 'text/plain'],  'Method not allowed');
        case FastRoute\Dispatcher::FOUND:
            return $routeInfo[1]($request, ... array_values($routeInfo[2]));
    }
});

$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:80', $loop);
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();