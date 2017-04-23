<?php

use Phalcon\Avatar\Gravatar;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashDirect;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flashSession', function () {
    return new FlashDirect([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->setShared(
    'dispatcher',
    function () {
        $eventsManager = new EventsManager();

        $eventsManager->attach(
            'dispatch:beforeException',
            function($event, $dispatcher, $exception) {
                switch ($exception->getCode()) {
                    case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action' => 'notFound',
                            )
                        );
                        return false;
                        break;
                    default:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action' => 'uncaughtException',
                            )
                        );
                        return false;
                        break;
                }
            }
        );

        $dispatcher = new Dispatcher();

        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    }
);

$di->setShared(
    'mailer',
    function () {
        require_once(BASE_PATH . './vendor/autoload.php');
        $config = $this->getConfig();
        $mailer = new \Phalcon\Ext\Mailer\Manager((array)$config->mail);
        return $mailer;
    }
);

$di->setShared('gravatar', function () {
    $config = $this->getConfig();
    $gravatar = new Gravatar($config->gravatar);
    return $gravatar;
});

$di->setShared('dropbox', function () {
    $config = $this->getConfig();
    $appinfo = new \Dropbox\AppInfo($config->dropbox->key, $config->dropbox->secret);
    return new \Dropbox\WebAuthNoRedirect($appinfo, 'PHP-FLOPPY.net/1.0');
});

