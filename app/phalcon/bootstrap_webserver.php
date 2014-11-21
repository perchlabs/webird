<?php
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
$di = new Phalcon\DI\FactoryDefault();

$di->setShared('config', function() {
    $config = require(__DIR__ . "/config/config.php");
    return $config;
});
$config = $di->get('config');

if (!file_exists($config->path->tmpDir)) {
    mkdir($config->path->tmpDir);
}

// Setup composer autoloading so that it doesn't need to be specified in each Module
require_once($config->path->composerDir . '/autoload.php');

require ($config->path->configDir . '/di.php');
require ($config->path->configDir . '/di_webserver.php');

if (DEV) {
    class_alias('\Webird\Debug', '\Dbg', true);
}

// Handle the request and inject DI
$application = new \Phalcon\Mvc\Application($di);
$application->registerModules([
    'web'   => ['className' => 'Webird\Web\Module'],
    'admin' => ['className' => 'Webird\Admin\Module'],
    'api'   => ['className' => 'Webird\Api\Module']
]);

try {
    echo $application->handle()->getContent();
} catch (\Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    exit(1);
}
