<?php

namespace spec\Raptor\Application\DependencyInjection;

use PhpSpec\ObjectBehavior;

use Raptor\Application\DependencyInjection\ContainerBuilder;
use Pimple\Container;

class ContainerBuilderSpec extends ObjectBehavior
{
    private $config = array(
        //standard object construction
        'epoch' => array(
            'class' => '\DateTimeImmutable',
            'arguments' => array(
                'date' => '1981-07-15 03:11:00'
            )
        ),
        //using another service as a factory
        'seven' => array(
            'factory' => array('@epoch', 'modify'),
            'arguments' => array(
                '+7 years'
            )
        ),
        //using a static method as a factory
        'ninetythree' => array(
            'factory' => array('\DateTime', 'createFromFormat'),
            'arguments' => array(
                'Y-m-d H:i:s',
                '1993-07-15 03:11:00'
            )
        ),
        //using another service as a parameter
        'diff' => array(
            'factory' => array('@epoch', 'diff'),
            'arguments' => array('@ninetythree')
        )
    );

    function it_is_initializable()
    {
        $this->shouldHaveType(ContainerBuilder::class);
    }

    function it_can_build_from_array()
    {
        $container = new Container;
        $result = $this->build($this->config, $container);

        $result->offsetGet('epoch')->shouldBeLike(new \DateTimeImmutable('1981-07-15 03:11:00'));
        $result->offsetGet('seven')->shouldBeLike(new \DateTimeImmutable('1988-07-15 03:11:00'));
        $result->offsetGet('ninetythree')->shouldBeLike(new \DateTime('1993-07-15 03:11:00'));

        $epoch = new \DateTimeImmutable('1981-07-15 03:11:00');
        $result->offsetGet('diff')->shouldBeLike($epoch->diff(new \DateTime('1993-07-15 03:11:00')));

        $result->offsetGet('container')->shouldBe($container);
    }
}