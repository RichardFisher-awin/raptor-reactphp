<?php
return [
    'routes' => [
        [
            'route' => '/',
            'controller' => 'controller.welcome',
            'action' => 'indexAction',
            'method' => 'GET'
        ],
        [
            'route' => '/hello/{name:\w+}',
            'controller' => 'controller.welcome',
            'action' => 'helloAction',
            'method' => 'GET'
        ],
        [
            'route' => '/sleep/{seconds:\d+}',
            'controller' => 'controller.slow',
            'action' => 'sleepAction',
            'method' => 'GET'
        ],
    ]
];