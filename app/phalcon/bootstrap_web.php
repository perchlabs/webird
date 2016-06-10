<?php
use Phalcon\DI\FactoryDefault as DI,
    Phalcon\Mvc\Application;

if (! defined('ENV')) {
  error_log('Error: The application ENV constant is not set.');
  exit(1);
}

define('DEV_ENV', 'dev');
define('TEST_ENV', 'test');
define('DIST_ENV', 'dist');
define('DEV', (ENV === DEV_ENV));

/*
 * Environment setup
 *
 * Different environments will require different levels of error reporting.
*/
switch (ENV)
{
    case DEV_ENV:
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        (new Phalcon\Debug)->listen();
        break;
    // case 'test':
    //     ini_set("display_errors", 0);
    //     ini_set("log_errors", 1);
    //     error_reporting(E_ALL);
    //     break;
    case DIST_ENV:
        ini_set("display_errors", 0);
        ini_set("log_errors", 1);
        error_reporting(E_ALL);
        break;
    default:
        error_log('Error: The application ENV constant is not set correctly.');
        exit(1);
}

// Create the dependency injector for the Phalcon framework
$di = new DI();
require_once(__DIR__ . '/config/services.php');
require_once(__DIR__ . '/config/services_web.php');
$config = $di->getConfig();
$di->getLoader();

if (!file_exists($config->path->tmpDir)) {
    mkdir($config->path->tmpDir);
}

if (DEV) {
    class_alias('\Webird\Debug', '\Dbg', true);
}

// Handle the request and inject DI
$application = new Application($di);
$application->registerModules([
    'web'   => ['className' => 'Webird\Modules\Web\Module'],
    'admin' => ['className' => 'Webird\Modules\Admin\Module'],
    'api'   => ['className' => 'Webird\Modules\Api\Module']
]);

try {
    echo $application->handle()->getContent();
} catch (\Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    exit(1);
}
