<?php
namespace Raptor\Application\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\Promise\Promise;

class SlowController
{
    /** @var LoopInterface */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function sleepAction(Request $request, int $seconds)
    {
        $loop = $this->loop;
        return new Promise(function ($resolve, $reject) use ($loop, $seconds) {
            $loop->addTimer($seconds, function() use ($resolve, $seconds) {
                $response = new Response(200, ['Content-Type' => 'text/plain'], "Responding after $seconds seconds!");
                $resolve($response);
            });
        });
    }
}