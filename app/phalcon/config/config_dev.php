<?php
use Phalcon\Config;

$projectDir = realpath(__DIR__ . '/../../..') . '/';
$appDir = $projectDir . 'app/';
$etcDir = $projectDir . 'etc/';
$devDir = $projectDir . 'dev/';
$buildDir = $projectDir . 'build/';
$composerDir = $devDir . 'vendor/';
$cacheDir = $tmpDir . 'cache/';

// Clear cache for gettext and other extensions
clearstatcache();

// Create the path to the temporary Volt cache directory
exec("mkdir -p " . escapeshellarg($cacheDir . 'volt/'));
exec("mkdir -p " . escapeshellarg($cacheDir . 'locale/'));

$config = new Config(yaml_parse_file("{$etcDir}/dev_defaults.yml"));
$config2 = new Config(yaml_parse_file("{$etcDir}/dev.yml"));
$config3 = new Config([
    DEV_ENV => [
        'path' => [
            'projectDir' => $projectDir,
            'etcDir'     => $etcDir,
            'devDir'     => $devDir,
            'buildDir'    => $buildDir
        ]
    ]
]);
$config4 = new Config([
    'locale' => yaml_parse_file("$appDir/locale/config.yml")
]);


$config->merge($config2);
$config->merge($config3);
$config->merge($config4);

return $config;
