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
exec("mkdir -p " . escapeshellarg($cacheDir . 'locale/'));

$config = new AdapterJson("{$etcDir}/dev_defaults.json");
$config2 = new AdapterJson("{$etcDir}/dev.json");
$config3 = new Config([
    'dev' => [
        'path' => [
            'projectDir' => $projectDir,
            'etcDir'     => $etcDir,
            'devDir'     => $devDir,
            'distDir'    => $distDir
        ]
    ]
]);
$config4 = new Config([
    'locale' => json_decode(file_get_contents("$appDir/locale/config.json"), true)
]);


$config->merge($config2);
$config->merge($config3);
$config->merge($config4);

return $config;
