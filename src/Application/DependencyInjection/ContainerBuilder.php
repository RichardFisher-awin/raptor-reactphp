<?php
namespace Raptor\Application\DependencyInjection;

use Pimple\Container;
use Closure;

class ContainerBuilder
{
    public function build(array $config, Container $container = null) : Container
    {
        $container = $container ?: new Container();
        $argumentBuilder = $this->getArgumentBuilder($container);

        foreach ($config as $key => $signature) {
            $container->offsetSet($key, $this->getCallback($container, $argumentBuilder, $signature));
        }

        $container['container'] = $container;

        return $container;
    }

    private function getCallback(Container $container, Closure $argumentBuilder, array $signature) : Closure
    {
        return function() use ($argumentBuilder, $signature, $container) {
            $computedArguments = $argumentBuilder($signature);

            if (! isset($signature['factory'])) {
                $class = $signature['class'];
                return new $class(...$computedArguments);
            }

            list($factory, $method) = $signature['factory'];

            if (strpos($factory, '@') === 0) {
                $factoryObject = $container->offsetGet(substr($factory, 1));
                return call_user_func_array(array($factoryObject, $method), $computedArguments);
            }

            return call_user_func_array(array($factory, $method), $computedArguments);

        };
    }

    private function getArgumentBuilder(Container $container) : Closure
    {
        return function($signature) use ($container) {
            $computedArguments = array();

            if (isset($signature['arguments'])) {
                foreach ($signature['arguments'] as $argument) {
                    if (! is_array($argument) && strpos($argument, '@') === 0) {
                        $computedArguments[] = $container->offsetGet(substr($argument, 1));
                    } else {
                        $computedArguments[] = $argument;
                    }
                }
            }

            return $computedArguments;
        };
    }
}