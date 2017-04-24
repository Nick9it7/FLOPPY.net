<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => 'drakonnik1',
        'dbname'      => 'test_db',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'formsDir'       => APP_PATH . '/forms/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ],
    'mail' => [
        'driver' 	 => 'smtp',
        'host'	 	 => 'smtp.gmail.com',
        'port'	 	 => 465,
        'encryption' => 'ssl',
        'username'   => 'floppy.dot.net@gmail.com',
        'password'	 => 'zubaqsclnelrbaak',
        'from'		 => [
            'email' => 'floppy.dot.net@gmail.com',
            'name'	=> 'FLOPPY.net'
        ]
    ],
    'gravatar' => [
        'default_image' => 'mm',
        'rating'        => 'x',
        'size'          => 200,
        'use_https'     => true,
    ],
    'dropbox' => [
        'key'    => 'weumrlj2xfjnp58',
        'secret' => 'gl9rtzntuc0wij7',
        'access' => 'vBcAZ0ux1_AAAAAAAAAAKkiOsbGnr-sEJXA7DaWklzT8UKMLEwbRRRyHF3n0P8L8'
    ]
]);
