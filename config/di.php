<?php
return [
    'services' => [
        'controller.welcome' => [
            'class' => "\\Raptor\\Application\\Controller\\WelcomeController"
        ],
        'controller.slow' => [
            'class' => "\\Raptor\\Application\\Controller\\SlowController",
            "arguments" => ['@loop']
        ],
        'controller.healthcheck' => [
            'class' => "\\Raptor\\Application\\Controller\\HealthcheckController",
            "arguments" => ['@loop']
        ]
    ]
];