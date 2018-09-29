<?php
namespace Raptor\Application\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Http\Response;

class WelcomeController
{
    public function indexAction(Request $request)
    {
        return new Response(200, ['Content-Type' => 'text/plain'], "Welcome!\n");
    }

    public function helloAction(Request $request, string $name)
    {
        return new Response(200, ['Content-Type' => 'text/plain'], "Hello, $name\n");
    }
}