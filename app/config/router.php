<?php

$router = $di->getRouter();

$router->add(
    '/recoverPassword/:params',
    [
        'controller'  => 'user',
        'action'      => 'recoverPassword',
        'hash'        => 1
    ]
);

$router->add(
    '/recoverPasswordCancel/:params',
    [
        'controller'  => 'user',
        'action'      => 'recoverPasswordCancel',
        'hash'        => 1
    ]
);

$router->add(
    '/user/:action',
    [
        'controller'  => 'user',
        'action'      => 1
    ]
);

$router->handle();
