<?php

declare(strict_types=1);

use Doctrine\ORM\Mapping\Driver\AttributeDriver;

return [
    'doctrine' => [
        'driver' => [
            'application_entities' => [
                'class' => AttributeDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../../module/Application/src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Application\Entity' => 'application_entities',
                ],
            ],
        ],
    ],
];