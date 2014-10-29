<?php
use Phalcon\DI\FactoryDefault\CLI as DI,
    Phalcon\PhalconDebug,
    Phalcon\Exception As PhalconException,
    Webird\Console as WebirdConsole,
    Webird\Cli\Exception\ArgumentValidationException;

if (php_sapi_name() !== "cli") {
    error_log('Error: The CLI interface is not being called from the command line.');
    exit(1);
}
if (! defined('ENVIRONMENT')) {
    error_log('Error: ENVIRONMENT is not defined');
    exit(1);
}

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
require_once($config->path->composerDir . '/autoload.php');
// Configure essential services
require($config->path->configDir . '/di.php');


$di = DI::getDefault();
$console = new WebirdConsole($di, 'server', $argv);
// Inject the console back into the DI to enabled it to handle batch tasks inside of a task
$di->setShared('console', $console);

$classes = $di->get('loader')->getClasses();
$console->registerModules([
    'cli' => [
        'className' => 'Webird\Cli\Module',
        'path'      => $classes['Webird\Cli\Module']
    ]
]);

try {
    $console->handle([
        'module' => 'cli'
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
