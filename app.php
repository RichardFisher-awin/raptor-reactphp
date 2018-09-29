<?php
use React\EventLoop\Factory;
use Raptor\Application\Application;

require __DIR__ . '/vendor/autoload.php';

$loop = Factory::create();
$application = new Application(realpath(__DIR__ . '/config'));
$application->web($loop);
$loop->run();