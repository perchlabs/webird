<?php
use Phalcon\Loader,
    Phalcon\Crypt,
    Phalcon\Filter,
    Phalcon\Security,
    Phalcon\Escaper,
    Phalcon\Tag,
    Phalcon\Annotations\Adapter\Memory as AnnotationsAdapter,
    Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Manager as ModelManager,
    Phalcon\Mvc\Model\MetaData\Memory as ModelMetaData,
    Phalcon\Mvc\Model\Transaction\Manager as TransactionManager,
    Phalcon\Mvc\Url,
    Phalcon\Mvc\View\Engine\Volt,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Logger\Multiple as MultipleStreamLogger,
    Phalcon\Logger\Adapter\File as FileLogger,
    Phalcon\Logger\Adapter\Firephp as FirephpLogger,
    Webird\Mvc\View\Simple as ViewSimple,
    Webird\Acl\Acl,
    Webird\Locale\Locale,
    Webird\Locale\Gettext,
    Webird\Mailer\Manager as MailManager,
    Webird\Logger\Adapter\Error as ErrorLogger,
    Webird\Logger\Adapter\Firelogger as Firelogger;

/**
 *
 */
Model::setup([
    'phqlLiterals'       => false,
    'notNullValidations' => false
]);

/**
 *
 */
$di->setShared('config', function() {
    return require(__DIR__ . "/config.php");
});

/**
 *
 */
$di->setShared('modelsManager', function() {
    return new ModelManager();
});

/**
 *
 */
$di->setShared('modelsMetadata', function() {
    return new ModelMetaData();
});

/**
 *
 */
$di->setShared('filter', function() {
    return new Filter();
});

/**
 *
 */
$di->setShared('tag', function() {
    return new Tag();
});

/**
 *
 */
$di->setShared('escaper', function() {
    return new Escaper();
});

/**
 *
 */
$di->setShared('annotations', function() {
    if (DEVELOPING && $this->getConfig()->dev->phpEncode) {
        throw new \Exception('Annotations cannot be used if PHP is being encoded because they will be removed.');
    }

    return new AnnotationsAdapter();
});

/**
 *
 */
$di->setShared('security', function() {
    return new Security();
});

/**
 *
 */
$di->setShared('eventsManager', function() {
    return new EventsManager();
});

/**
 *
 */
$di->setShared('transactionManager', function() {
    return new TransactionManager();
});

/**
 *
 */
$di->set('loader', function() {
    $config = $this->getConfig();
    $commonDir = $config->path->commonDir;
    $modulesDir = $config->path->modulesDir;

    $loader = new Loader();
    $loader->setExtensions(['php']);

    // Setup composer autoloading so that it doesn't need to be specified in each Module
    require_once($config->path->composerDir . 'autoload.php');

    $loader->registerNamespaces([
        'Webird\Models'       => "$commonDir/models",
        'Webird\Forms'        => "$commonDir/forms",
        'Webird\Plugins'      => "$commonDir/plugins",
        'Webird'              => "$commonDir/library",
    ]);

    // Register module classes
    $classes = [];
    foreach ($config->app->modules as $moduleName) {
        $class = 'Webird\\Modules\\' . ucfirst($moduleName) . '\\Module';
        $classes[$class] = $modulesDir . $moduleName . '/Module.php';
    }
    $loader->registerClasses($classes, true);

    $loader->register();
    return $loader;
});

/**
 *
 */
$di->setShared('db', function() {
    $config = $this->getConfig();

    return new DbAdapter([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => 'utf8'
    ]);
});

/**
 *
 */
$voltService = function($view) {
    $config = $this->getConfig();

    $phalconDir = $config->path->phalconDir;
    $voltCacheDir = $config->path->voltCacheDir;
    $configDir = $config->path->configDir;

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

    $volt = new Volt($view, $this);
    $volt->setOptions([
        'compileAlways' => $compileAlways,
        'stat' => $stat,
        'compiledPath' => function($templatePath) use ($voltCacheDir, $phalconDir) {
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

            return "{$voltCacheDir}{$templateFrag}.php";
        }
    ]);

    $compiler = $volt->getCompiler();
    require("{$configDir}volt_compiler.php");

    return $volt;
};

/**
 *
 */
$di->set('voltService', $voltService);

/**
 *
 */
$di->set('viewSimple', function() use ($voltService) {
    $config = $this->getConfig();

    $view = new ViewSimple();
    $view->setDI($this);
    $view->registerEngines([
        '.volt' => \Closure::bind($voltService, $this)
    ]);
    $view->setViewsDir($config->path->viewsSimpleDir);
    return $view;
});

/**
 *
 */
$di->setShared('locale', function() {
    $config = $this->getConfig();

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

    return new Locale($this, $config->locale->default, $supported, $config->locale->map);
});

/**
 *
 */
$di->setShared('translate', function() {
    $config = $this->getConfig();
    $locale = $this->getLocale();

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
$di->setShared('debug', function() {
    $config = $this->getConfig();

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
$di->setShared('mailer', function() {
    $config = $this->getConfig();

    $mailManager = new MailManager($config->mailer, $config->site->mail);
    $mailManager->setDI($this);
    return $mailManager;
});

/**
 *
 */
$di->set('crypt', function() {
    $config = $this->getConfig();

    $crypt = new Crypt();
    $crypt->setKey($config->security->cryptKey);
    return $crypt;
});

/**
 * Access Control List
 */
$di->set('acl', function() {
    $configDir = $this->getConfig()
        ->path->configDir;

    return new Acl(require("$configDir/acl.php"));
});

// /**
//  *
//  */
// $di->setShared('url', function() {
//     $config = $this->getConfig();
//
//     $isCurrentlyHttps = $config->server->https;
//     $shouldHttps = $config->security->https;
//     $usingHsts = ($config->security->hsts > 0);
//
//     $proto = ($isCurrentlyHttps || $shouldHttps || $usingHsts) ? 'https' : 'http';
//
//     if ($config->server->domain == '') {
//         $domain = $config->site->domains[0];
//     } else {
//         $domain = $config->server->domain;
//     }
//
//     $uri = $config->app->uriPathPrefix;
//
//     $url = new Url();
//     $url->seturiPathPrefix("{$proto}://{$domain}{$uri}");
//     return $url;
// });

/**
 *
 */
$di->setShared('url', function() {
    $config = $this->getConfig();

    $proto = $config->server->proto;
    $domain = ($config->server->domain == '') ? $config->site->domains[0] : $config->server->domain;
    $uriPathPrefix = $config->app->uriPathPrefix;

    $url = new Url();
    $url->setBaseUri("{$proto}://{$domain}{$uriPathPrefix}");
    return $url;
});
