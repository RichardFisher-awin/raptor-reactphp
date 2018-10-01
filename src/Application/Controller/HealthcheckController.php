<?php
namespace Raptor\Application\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use React\MySQL\QueryResult;
use React\Promise\Promise;

class HealthcheckController
{
    /** @var LoopInterface */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function indexAction(Request $request)
    {
        $loop = $this->loop;
        error_log('before promise');
        error_log('before factory');
        $factory = new Factory($loop);
        error_log('after factory');
        $uri = 'reacttest_ro:passwordRo@mysql.local/reacttest';
        return $factory->createConnection($uri)->then(function (ConnectionInterface $connection) {
            error_log('inside createConnection');
            return $connection->query('SHOW TABLES')->then(
                function (QueryResult $command) {
                    error_log('inside QueryResult');
                    error_log(print_r($command->resultRows,true));
                    return new Response(200, ['Content-Type' => 'text/plain'],  print_r($command->resultRows, true));
                },
                function (\Exception $error) {
                    error_log($error->getMessage());
                }
            );

            $connection->quit();
        });
    }
}