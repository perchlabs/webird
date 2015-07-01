<?php
use Phalcon\Loader,
    Phalcon\Mvc\Model,
    Phalcon\Mvc\Url,
    Phalcon\Crypt,
    Phalcon\Mvc\View\Engine\Volt,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Logger\Multiple as MultipleStreamLogger,
    Phalcon\Logger\Adapter\File as FileLogger,
    Phalcon\Logger\Adapter\Firephp as FirephpLogger,
    Webird\Module,
    Webird\Mvc\View\Simple as ViewSimple,
    Webird\Acl\Acl,
    Webird\DatabaseSessionReader,
    Webird\Locale\Locale,
    Webird\Locale\Gettext,
    Webird\Mailer\Manager as MailManager,
    Webird\Logger\Adapter\Error as ErrorLogger,
    Webird\Logger\Adapter\Firelogger as Firelogger;

/**
 *
 */
Model::setup([
    'phqlLiterals'       => true,
    'notNullValidations' => false
]);

/**
 *
 */
$di->set('loader', function() use ($config) {
    $commonDir = $config->path->commonDir;
    $modulesDir = $config->path->modulesDir;

    $loader = new Loader();
    $loader->setExtensions(['php']);

    $loader->registerNamespaces([
        'Webird\Models'       => "$commonDir/models",
        'Webird\Forms'        => "$commonDir/forms",
        'Webird\Plugins'      => "$commonDir/plugins",
        'Webird'              => "$commonDir/library",
    ]);
    $loader->register();

    $classes = [];
    foreach ($config->app->modules as $moduleName) {
        $class = 'Webird\\' . ucfirst($moduleName) . '\\Module';
        $path  = Module::moduleNameToDir($moduleName) . 'Module.php';
        $classes[$class] = $path;
    }
    $loader->registerClasses($classes, true);
    $loader->register();

    return $loader;
});
$di->get('loader');

/**
 *
 */
$di->setShared('db', function() use ($di) {
    $config = $di->get('config');

    return new DbAdapter([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ]);
});

/**
 *
 */
$di->set('sessionReader', function() use ($di) {
    $config = $di->get('config');
    $connection = $di->get('db');

    $sessionReader = new DatabaseSessionReader([
        'db'          => $connection,
        'unique_id'   => $config->session->unique_id,
        'db_table'    => $config->session->db_table,
        'db_id_col'   => $config->session->db_id_col,
        'db_data_col' => $config->session->db_data_col,
        'db_time_col' => $config->session->db_time_col,
        'uniqueId'    => $config->session->unique_id
    ]);
    return $sessionReader;
});

/**
 *
 */
$voltService = function($view, $di) {
    $config = $di->get('config');
    $voltCacheDir = $config->path->voltCacheDir;

    switch (ENV) {
        case DIST_ENV:
            $compileAlways = false;
            $stat = false;
            break;
        case DEV_ENV:
            $compileAlways = true;
            $stat = true;
            break;
    }

    $volt = new Volt($view, $di);
    $volt->setOptions([
        'compileAlways' => $compileAlways,
        'stat' => $stat,
        'compiledPath' => function($templatePath) use ($view, $voltCacheDir) {
            $config = $view->getDI()->get('config');
            $phalconDir = $config->path->phalconDir;

            // This makes the phalcon view path into a portable fragment
            $templateFrag = str_replace($phalconDir, '', $templatePath);
            // Allows modules to share the compiled layouts and partials paths
            $templateFrag = preg_replace('/^modules\/[a-z]+\/views\/(_partials|_layouts)\/..\/..\/..\/..\//', '', $templateFrag);
            // Allows modules to share the compiled layouts and partials paths
            $templateFrag = preg_replace('/modules\/[a-z]+\/views\/..\/..\/..\//', '', $templateFrag);
            // Replace '/' with a safe '%%'
            $templateFrag = str_replace('/', '%%', $templateFrag);

            if (strpos($templateFrag, '..') !== false) {
                throw new \Exception('Error: template fragment has ".." in path.');
            }

            $voltCompiledPath = "{$voltCacheDir}{$templateFrag}.php";
            return $voltCompiledPath;
        }
    ]);

    $compiler = $volt->getCompiler();
    require($config->path->phalconDir . '/config/volt_compiler.php');

    return $volt;
};

/**
 *
 */
$di->set('voltService', $voltService);

/**
 *
 */
$di->set('viewSimple', function() use ($di, $voltService) {
    $config = $di->get('config');

    $view = new ViewSimple();
    $view->setDI($di);

    $view->registerEngines([
        '.volt' => $voltService
    ]);

    $view->setViewsDir($config->path->viewsSimpleDir);

    return $view;
});

/**
 *
 */
$di->setShared('locale', function() use ($di) {
    $config = $di->get('config');

    switch (ENV) {
        case DIST_ENV:
            $supported = $config->locale->supported;
            break;
        case DEV_ENV:
            $supported = [];
            foreach(glob($config->path->localeDir . '/*', GLOB_ONLYDIR) as $locale) {
                $supported[basename($locale)] = 1;
            }
            break;
    }

    $locale = new Locale($di, $config->locale->default, $supported, $config->locale->map);
    return $locale;
});

/**
 *
 */
$di->setShared('translate', function() use ($di) {
    $config = $di->get('config');
    $locale = $di->get('locale');

    switch (ENV) {
        case DIST_ENV:
            $compileAlways = false;
            break;
        case DEV_ENV:
            $compileAlways = true;
            break;
    }

    $gettext = new Gettext();
    $gettext->setOptions([
        'compileAlways'  => $compileAlways,
        'locale'         => $locale->getBestLocale(),
        'supported'      => $locale->getSupportedLocales(),
        'domains'        => $config->locale->domains,
        'localeDir'      => $config->path->localeDir,
        'localeCacheDir' => $config->path->localeCacheDir
    ]);

    return $gettext;
});

/**
 *
 */
$di->setShared('debug', function() use ($di) {
    $config = $di->getConfig();

    $logger = new MultipleStreamLogger();
    switch (ENV) {
        case DEV_ENV:
            $logger->push(new ErrorLogger());
            if ('cli' != php_sapi_name()) {
                $debugLogFile = str_replace('{{name}}', $config->site->domains[0],
                    $config->dev->path->debugLog);
                $fileLogger = new FileLogger($debugLogFile);
                $fileLogger->getFormatter()->setFormat('%message%');
                $logger->push($fileLogger);

                $logger->push(new Firelogger());
                $logger->push(new FirephpLogger(''));
            }
        break;
    }
    return $logger;
});

/**
 * Mail service
 */
$di->setShared('mailer', function() use ($di) {
    $config = $di->get('config');

    $mailManager = new MailManager($config->mailer, $config->site->mail);
    $mailManager->setDI($di);

    return $mailManager;
});

/**
 *
 */
$di->set('crypt', function() use ($di) {
    $config = $di->get('config');

    $crypt = new Crypt();
    $crypt->setKey($config->security->cryptKey);
    return $crypt;
});

/**
 * Access Control List
 */
$di->set('acl', function() use ($di) {
    $configDir = $di->getConfig()->path->configDir;

    $aclData = require("$configDir/acl.php");
    $acl = new Acl($aclData);

    return $acl;
});

/**
 *
 */
$di->setShared('url', function() use ($di) {
    $config = $di->get('config');

    $isCurrentlyHttps = $config->server->https;
    $shouldHttps = $config->security->https;
    $usingHsts = ($config->security->hsts > 0);

    $proto = ($isCurrentlyHttps || $shouldHttps || $usingHsts) ? 'https' : 'http';

    if ($config->server->domain == '') {
        $domain = $config->site->domains[0];
    } else {
        $domain = $config->server->domain;
    }

    $uri = $config->app->baseUri;

    $url = new Url();
    $url->setBaseUri("{$proto}://{$domain}{$uri}");
    return $url;
});
