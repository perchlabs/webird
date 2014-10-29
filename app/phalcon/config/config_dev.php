<?php
use Phalcon\Config,
    Phalcon\Config\Adapter\Json as AdapterJson;

$projectDir = realpath(__DIR__ . '/../../..') . '/';
$appDir = $projectDir . 'app/';
$etcDir = $projectDir . 'etc/';
$devDir = $projectDir . 'dev/';
$distDir = $projectDir . 'dist/';
$composerDir = $devDir . 'vendor/';
$cacheDir = $tmpDir . 'cache/';

// Clear cache for gettext and other extensions
clearstatcache();

// Create the path to the temporary Volt cache directory
exec("mkdir -p " . escapeshellarg($cacheDir . 'volt/'));

// Load the base json config file
$customConfig = new AdapterJson("{$etcDir}/dev_defaults.json");
// Load the configurable json config file
$customConfig2 = new AdapterJson("{$etcDir}/dev.json");
// Set dev specific paths
$devConfig = new Config([
    'dev' => [
        'path' => [
            'projectDir' => $projectDir,
            'etcDir'     => $etcDir,
            'devDir'     => $devDir,
            'distDir'    => $distDir
        ]
    ]
]);

// Override the default
$customConfig->merge($customConfig2);
$customConfig->merge($devConfig);
