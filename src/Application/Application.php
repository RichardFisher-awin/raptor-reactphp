<?php
namespace Raptor\Application;

use Noodlehaus\Config;
use Pimple\Container;
use Raptor\Application\DependencyInjection\ContainerBuilder;
use Raptor\Application\Webserver\Factory;
use React\EventLoop\LoopInterface;

class Application
{
    /** @var string  */
    private $configDirectory;

    /** @var Config */
    private $config;

    /** @var Container */
    private $container;

    public function __construct(string $configDirectory)
    {
        $this->configDirectory = $configDirectory;
    }

    public function web(LoopInterface $loop, string $bind = '0.0.0.0:80')
    {
        $serverFactory = new Factory();
        $container = $this->getContainer();
        $container->offsetSet('loop', $loop);

        $server = $serverFactory->buildServer($container, $this->getConfig()->get('routes'));

        $socket = new \React\Socket\Server($bind, $loop);

        $server->listen($socket);
    }

    private function getContainer() : Container
    {
        if (! $this->container) {
            $containerBuilder = new ContainerBuilder();
            $this->container = $containerBuilder->build($this->getConfig()->get('services', []));
        }

        return $this->container;
    }

    private function getConfig()
    {
        if (! $this->config) {
            $this->config = new Config($this->configDirectory);
        }

        return $this->config;
    }
}