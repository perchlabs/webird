<?php
use Phalcon\Config;
use Phalcon\Config\Adapter\Json;

$projectDir = realpath(__DIR__ . '/../../..') . '/';
$appDir = $projectDir . 'app/';
$etcDir = $projectDir . 'etc/';
$devDir = $projectDir . 'dev/';
$prodDir = $projectDir . 'prod/';
$composerDir = $projectDir . 'vendor/';
$cacheDir = $tmpDir . 'cache/';

// Clear cache for gettext and other extensions
clearstatcache();

// Create the path to the temporary Volt cache directory
exec("mkdir -p " . escapeshellarg($cacheDir . 'volt/'));
exec("mkdir -p " . escapeshellarg($cacheDir . 'locale/'));

$config = new Json("{$etcDir}dev_defaults.json");
$config2 = new Json("{$etcDir}dev.json");
$config3 = new Config([
    DEV_ENV => [
        'path' => [
            'projectDir' => $projectDir,
            'etcDir'     => $etcDir,
            'devDir'     => $devDir,
            'prodDir'    => $prodDir,
        ],
    ],
]);
$config4 = new Config([
    'locale' => new Json("{$appDir}locale/config.json"),
]);

$config->merge($config2);
$config->merge($config3);
$config->merge($config4);

return $config;
