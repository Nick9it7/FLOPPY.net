<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'root',
                    'dbname'   => 'test_db',
                    'encoding' => 'utf8',
                ]
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'proxy_dir' => 'data/doctrine/proxy',
                'proxy_namespace' => 'Doctrine\Proxy',
            ]
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/doctrine/migrations',
                'namespace' => 'Migrations',
                'table' => 'migrations',
            ],
        ],
        'driver' => [
            'application_entities' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    'module/Application/src/Model'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    'Application\Model' => 'application_entities',
                ],
            ],
        ],
    ]
];
