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
    ]
];