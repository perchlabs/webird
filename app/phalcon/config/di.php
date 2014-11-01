<?php
use Phalcon\Loader,
    Phalcon\Mvc\Url,
    Phalcon\Crypt,
    Phalcon\Mvc\View\Engine\Volt,
    Phalcon\Mvc\View\Simple as ViewSimple,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Webird\Acl\Acl,
    Webird\DatabaseSessionReader,
    Webird\Translate\Gettext,
    Webird\Mailer\Manager as MailManager;

$di->set('loader', function() use ($config) {
    $commonDir = $config->path->commonDir;
    $modulesDir = $config->path->modulesDir;

    $loader = new Loader();
    $loader->setExtensions(['php']);

    $loader->registerNamespaces([
        'Webird\Controllers'  => "$commonDir/controllers",
        'Webird\Models'       => "$commonDir/models",
        'Webird\Forms'        => "$commonDir/forms",
        'Webird\Plugins'      => "$commonDir/plugins",
        'Webird'              => "$commonDir/library",
    ]);

    $loader->registerClasses([
        'Webird\Web\Module'   => "$modulesDir/web/Module.php",
        'Webird\Admin\Module' => "$modulesDir/admin/Module.php",
        'Webird\Api\Module'   => "$modulesDir/api/Module.php",
        'Webird\Cli\Module'   => "$modulesDir/cli/Module.php"
    ], true);

    $loader->register();

    return $loader;
});
$di->get('loader')->register();






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







$di->set('sessionReader', function() use ($di) {
try {
    $config = $di->get('config');
    $connection = $di->get('db');

    $sessionReader = new DatabaseSessionReader([
        'db'          => $connection,
        'unique_id'    => $config->session->unique_id,
        'db_table'    => $config->session->db_table,
        'db_id_col'   => $config->session->db_id_col,
        'db_data_col' => $config->session->db_data_col,
        'db_time_col' => $config->session->db_time_col,
        'uniqueId'    => $config->session->unique_id
    ]);

} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}

    return $sessionReader;
});






$voltService = function($view, $di) {
    $config = $di->get('config');
    $voltCompileDir = $config->path->voltCompileDir;

    switch (ENVIRONMENT) {
        case 'dist':
            $compileAlways = false;
            $stat = false;
            break;
        case 'dev':
            $compileAlways = true;
            $stat = true;
            break;
    }

    $volt = new Volt($view, $di);
    $volt->setOptions([
        'compileAlways' => $compileAlways,
        'stat' => $stat,
        'compiledPath' => function($templatePath) use ($view, $voltCompileDir) {
            $config = $view->getDI()->get('config');
            $phalconDir = $config->path->phalconDir;

            // This makes the phalcon view path into a portable fragment
            $templateFrag = str_replace($phalconDir, '', $templatePath);
            // Allows modules to share the compiled layouts and partials paths
            $templateFrag = preg_replace('/^modules\/[a-z]+\/views\/..\/..\/..\//', '', $templateFrag);
            // Replace '/' with a safe '%%'
            $templateFrag = str_replace('/', '%%', $templateFrag);

            if (strpos($templateFrag, '..') !== false) {
                throw new \Exception('Error: template fragment has ".." in path.');
            }

            $voltCompiledPath = "{$voltCompileDir}{$templateFrag}.php";
            return $voltCompiledPath;
        }
    ]);

    $compiler = $volt->getCompiler();
    require($config->path->phalconDir . '/config/volt_compiler.php');

    return $volt;
};






$di->set('voltService', $voltService);





$di->set('viewSimple', function() use ($di, $voltService) {
    $config = $di->get('config');

    $view = new ViewSimple();
    $view->setViewsDir($config->path->viewsSimpleDir);
    $view->registerEngines([
        '.volt' => $voltService
    ]);

    // TODO: Move this into a base class
    $view->setVars([
        'domain' => $config->site->domains[0],
        'link'   => $config->site->link
    ]);

    return $view;
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

    $translate = $di->get('translate');
    $aclData = require("$configDir/acl.php");
    $acl = new Acl($aclData);

    return $acl;
});






$di->setShared('translate', function() use ($di) {
    $config = $di->get('config');
    // Get the locale from the request headers
    $locale = $di->get('request')->getBestLanguage();

    // Ensure that the locale region separator is '-' and the region code is in lower case
    $locale = str_replace('-', '_', $locale);
    $localeParts = explode('_', $locale);
    $locale = $localeParts[0];
    if (count($localeParts) > 1)
      $locale .= '_' . strtoupper($localeParts[1]);

    $translate = new Gettext([
        'locale' => $locale,
        'domains' => [
            'messages' => $config->path->localeDir
        ]
    ]);

    return $translate;
});





$di->setShared('url', function() use ($di) {
    $config = $di->get('config');

    $isCurrentlyHttps = $config->server->https;
    $shouldHttps = $config->security->https;
    $usingHsts = ($config->security->hsts > 0);

    $proto = ($isCurrentlyHttps || $shouldHttps || $usingHsts) ? 'https' : 'http';

    if (empty($config->server->domain)) {
        $domain = $config->site->domains[0];
    } else {
        $domain = $config->server->domain;
    }

    $uri = $config->app->baseUri;

    $url = new Url();
    $url->setBaseUri("{$proto}://{$domain}{$uri}");
    return $url;
});
