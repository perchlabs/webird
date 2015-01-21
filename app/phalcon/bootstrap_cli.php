<?php
use Phalcon\DI\FactoryDefault\CLI as DI,
    Phalcon\PhalconDebug,
    Phalcon\Exception As PhalconException,
    Webird\CLI\Console as WebirdConsole,
    Webird\Cli\Exception\ArgumentValidationException;

if (php_sapi_name() !== "cli") {
    error_log('Error: The CLI interface is not being called from the command line.');
    exit(1);
}
if (! defined('ENV')) {
    error_log('Error: ENV is not defined');
    exit(1);
}

define('DEV_ENV', 'dev');
define('TEST_ENV', 'test');
define('DIST_ENV', 'dist');
define('DEV', (ENV === DEV_ENV));

// Create the dependency injector for the Phalcon framework
$di = new DI();

$di->setShared('config', function() {
    $config = require(__DIR__ . "/config/config.php");
    return $config;
});
$config = $di->get('config');

if (!file_exists($config->path->tmpDir)) {
    mkdir($config->path->tmpDir);
}

// Setup composer autoloading so that it doesn't need to be specified in each Module
require_once($config->path->composerDir . 'autoload.php');
// Configure essential services
require($config->path->configDir . 'services.php');

$di = DI::getDefault();
$console = new WebirdConsole($di);
// Inject the console back into the DI to enabled it to handle batch tasks inside of a task
$di->setShared('console', $console);
$console->registerModules([
    'cli' => ['className' => 'Webird\Cli\Module']
]);

if (DEV) {
    class_alias('\Webird\Debug', '\Dbg', true);
}
try {
    $console->handle([
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
