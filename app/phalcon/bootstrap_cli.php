<?php
use Phalcon\DI,
    Phalcon\Exception As PhalconException,
    Webird\Cli\Exception\ArgumentValidationException;

if (php_sapi_name() !== "cli") {
    error_log('Error: The CLI interface is not being called from the command line.');
    exit(1);
}
if (!defined('ENV')) {
    error_log('Error: ENV is not defined');
    exit(1);
}

define('DEV_ENV', 'dev');
define('TEST_ENV', 'test');
define('DIST_ENV', 'dist');
define('DEVELOPING', ENV === DEV_ENV);
define('TESTING', ENV === TEST_ENV);

// Create the dependency injector for the Phalcon framework
$di = new DI();
require __DIR__ . '/config/services.php';
require __DIR__ . '/config/services_cli.php';
$config = $di->getConfig();
$di->getLoader();

if (!file_exists($config->path->tmpDir)) {
    mkdir($config->path->tmpDir);
}

try {
    $console = $di->getConsole()
        ->handle([
            'module'     => 'cli',
            'defaultCmd' => 'server',
            'params'     => $argv
        ]);
}
catch (PhalconException $e) {
    error_log($e->getMessage());
    exit(255);
}
catch (\Exception $e) {
    error_log($e->getMessage());
    exit($e->getCode());
}
