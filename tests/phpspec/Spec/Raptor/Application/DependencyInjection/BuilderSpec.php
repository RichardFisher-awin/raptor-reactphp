<?php

namespace Spec\Raptor\Application\DependencyInjection;

use PhpSpec\ObjectBehavior;

use Raptor\Application\ContainerBuilder;

class BuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContainerBuilder::class);
    }

    function it_can_build_from_array()
    {
        $config = array(
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
            'twelve' => array(
                'factory' => array('\DateTime', 'createFromFormat'),
                'arguments' => array(
                    'Y-m-d H:i:s',
                    '1993-07-15 03:11:00'
                )
            ),
            //using another service as a parameter
            'diff' => array(
                'factory' => array('@epoch', 'diff'),
                'arguments' => array('@twelve')
            )
        );

        $result = $this->build($config);

        $result->offsetGet('epoch')->shouldBeLike(new \DateTimeImmutable('1981-07-15 03:11:00'));
        $result->offsetGet('seven')->shouldBeLike(new \DateTimeImmutable('1988-07-15 03:11:00'));
        $result->offsetGet('twelve')->shouldBeLike(new \DateTime('1993-07-15 03:11:00'));

        $epoch = new \DateTimeImmutable('1981-07-15 03:11:00');
        $result->offsetGet('diff')->shouldBeLike($epoch->diff(new \DateTime('1993-07-15 03:11:00')));
    }
}