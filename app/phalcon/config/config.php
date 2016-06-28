<?php
use Phalcon\Config;

$tmpDir = '/tmp/webird-' .  md5(__DIR__) . '-' . posix_geteuid() . '/';

switch (ENV) {
    case DIST_ENV:
        // At this point the compliled application is running in its portable directory
        $appDir = realpath(__DIR__ . '/../..') . '/';
        $cacheDir = $appDir . 'cache-static/';
        $composerDir = $appDir . 'vendor/';
        // Load the configurable json config file
        $config = new Config(yaml_parse_file("{$appDir}/etc/config.yml"));
        break;
    case DEV_ENV:
        $config = require_once(__DIR__ . '/config_dev.php');
        break;
    default:
        error_log('Invalid environment in configuration.');
        exit(1);
        break;
}

$config2 = new Config([
    'app' => [
        'baseUri'        => '/',
        'defaultPath'    => 'features',
        'modules'        => ['cli', 'web', 'api', 'admin']
    ],
    'locale' => [
        'domains' => ['phalcon']
    ],
    'path' => [
        // Typical Phalcon paths
        'appDir'         => $appDir,
        'phalconDir'     => $appDir . 'phalcon/',
        'configDir'      => $appDir . 'phalcon/config/',
        'modulesDir'     => $appDir . 'phalcon/modules/',
        'commonDir'      => $appDir . 'phalcon/common/',
        'viewsCommonDir' => $appDir . 'phalcon/common/views/',
        'viewsSimpleDir' => $appDir . 'phalcon/common/views/simple/',
        'localeDir'      => $appDir . 'locale/',
        // Paths that change with ENV
        'tmpDir'         => $tmpDir,
        'cacheDir'       => $cacheDir,
        'voltCacheDir'   => $cacheDir . 'volt/',
        'localeCacheDir' => $cacheDir . 'locale/',
        // Third party dependency paths
        'composerDir'    => $composerDir
    ],
    'database' => [
      'charset'          => 'utf8'
    ],
    // This is for settings that the server should be
    'security' => [
        'passwordMinLength' => 8
    ],
    // This is for settings that the server is in actuality
    'server' => [
        'domain'         => (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '',
        'proto'          => (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https' : 'http',
        'https'          => (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))
    ],
    'session' => [
        'table'          => 'session_data',
        'session_id'     => 'session_id',
        'data'           => 'data',
        'created_at'     => 'created_at',
        'modified_at'    => 'modified_at'
    ]
]);

// Merge it into main config
$config->merge($config2);

// Configure settings that require more calculation
$proto = ($config->security->https || $config->security->hsts > 0) ? 'https' : 'http';
$config->site->link = "{$proto}://" . $config->site->domains[0] . $config->app->baseUri;

return $config;
