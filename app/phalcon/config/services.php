<?php
use Phalcon\Loader;
use Phalcon\Crypt;
use Phalcon\Filter;
use Phalcon\Security;
use Phalcon\Escaper;
use Phalcon\Tag;
use Phalcon\Annotations\Adapter\Memory as AnnotationsAdapter;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\MetaData\Memory as ModelMetaData;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Logger\Multiple as MultipleStreamLogger;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Adapter\Firephp as FirephpLogger;
use Webird\Mvc\View\Simple as ViewSimple;
use Webird\Plugins\Devel as DevelPlugin;
use Webird\Acl\Acl;
use Webird\Locale\Locale;
use Webird\Locale\Gettext;
use Webird\Mailer\Manager as MailManager;
use Webird\Logger\Adapter\Error as ErrorLogger;
use Webird\Logger\Adapter\Firelogger as Firelogger;

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
    return require __DIR__ . "/config.php";
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

    $connection = new DbAdapter([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => 'utf8',
    ]);

    if (DEVELOPING) {
        $eventsManager = new EventsManager();
        $eventsManager->attach('db', $this->getDevel());
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});

/**
 *
 */
$di->setShared('devel', function() {
    if (!DEVELOPING) {
        throw new \Exception('The Debug plugin can only be used in the Development environment.');
    }

    $devel = new DevelPlugin();
    $devel->setDI($this);
    return $devel;
});

/**
 *
 */
$di->set('volt', function($view) {
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
        },
    ]);

    $compiler = $volt->getCompiler();
    require "{$configDir}volt_compiler.php";

    return $volt;
});

/**
 *
 */
$di->setShared('voltShared', function($view) {
    return $this->getVolt($view);
});

/**
 *
 */
$di->set('viewSimple', function() {
    $config = $this->getConfig();

    $view = new ViewSimple();
    $view->setDI($this);
    $view->registerEngines([
        '.volt' => 'volt',
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
        'localeCacheDir' => $config->path->localeCacheDir,
    ]);
    return $gettext;
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

    return new Acl(require "$configDir/acl.php");
});

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
