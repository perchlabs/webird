<?php
namespace Webird\Modules\Cli\Tasks;

use Phalcon\DI;
use Phalcon\Mvc\View\Engine\Volt\Compiler as Compiler;
use Phalcon\Mvc\View\Engine\Volt;
use Webird\CLI\Task;
use Webird\Locale\Compiler as LocaleCompiler;
use Webird\Locale\CompilerException as LocaleCompilerException;

/**
 * Task for Build
 *
 */
class BuildTask extends Task
{
    /**
     *
     */
    public function mainAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Build system',
            'args' => [
                'required' => [],
                'optional' => [],
            ],
            'opts' => [],
        ]);

        // Fix for missing services in CLI services.  If a service is missing then it will cause compiled Volt
        // templates that leave out the $this-> at the beginning due to the service not being available and so Volt
        // assumes that it is a variable.
        $diPrimary = $this->getDI();
        $di = new DI();
        require $this->config->path->configDir . 'services_web.php';
        foreach ($di->getServices() as $serviceName => $service) {
            if (!$diPrimary->has($serviceName)) {
                $diPrimary->set($serviceName, function() {});
            }
        }

        $this->cleanDirectoryStructure();
        $this->buildDirectoryStructure();

        // Build Volt templates first so that the locale messages can be
        // extracted in case of an error
        $this->compileVoltTemplates();
        $this->buildPhalconDir();
        $this->makeEntryPoints();
        $this->copyFiles();
        $this->buildConf();
        $this->installPackages();
        $this->compileLocales();
        $this->buildWebpack();
    }

    /**
     *
     */
    private function buildPhalconDir()
    {
        $config = $this->config;
        $phalconDir = $config->path->phalconDir;
        $prodDir = $config->dev->path->prodDir;

        $prodDirEsc = escapeshellarg($prodDir);
        $phalconAppDirEsc = escapeshellarg($phalconDir);
        $phalconBuildDirEsc = escapeshellarg($prodDir . 'phalcon');

        if (!isset($config->dev->phpEncode)) {
            throw new \Exception('The PHP Encoder value is not set.', 1);
        }
        $phpEncode = $config->dev->phpEncode;

        if (empty($phpEncode)) {
            `cp -R $phalconAppDirEsc $phalconBuildDirEsc`;
        } else {
            if (!isset($config->dev->phpEncoders[$phpEncode])) {
                throw new \Exception("The '$phpEncode' PHP encoder setting does not exist", 1);
            }

            $encoder = $config->dev->phpEncoders[$phpEncode];
            $encCmdEsc = escapeshellcmd($encoder->path);
            switch ($phpEncode) {
                case 'ioncube':
                    $cmd = "$encCmdEsc $phalconAppDirEsc --into $prodDirEsc --merge-target";
                    exec($cmd, $out, $ret);
                    break;
            }
        }
    }

    /**
     *
     */
    private function cleanDirectoryStructure()
    {
        $projectDir = $this->config->dev->path->projectDir;
        $prodDir = $this->config->dev->path->prodDir;
        $prodDirEsc = escapeshellarg($prodDir);

        // TODO: Add more checks for disasters against the rm -Rf command
        // Check for some disaster cases since the script will try to recursively delete the folder
        if ($prodDir != "{$projectDir}prod/" || $prodDir == '' || $prodDir == '/') {
            throw new \Exception('Critical Error: Attempting to delete build directory when it is not set correctly.');
        }
        if (file_exists($prodDir)) {
            exec("rm -Rf $prodDirEsc", $out, $ret);
            if ($ret != 0) {
                throw new \Exception('There was a problem deleting the build directory.');
            }
        }
    }

    /**
     *
     */
    private function buildDirectoryStructure()
    {
        $appDir = $this->config->path->appDir;
        $projectDir = $this->config->dev->path->projectDir;
        $prodDir = $this->config->dev->path->prodDir;

        mkdir($prodDir);
        mkdir($prodDir . 'public/');
        mkdir($prodDir . 'etc/');
        mkdir($prodDir . 'cache-static/');
        mkdir($prodDir . 'cache-static/volt/');
    }

    /**
     *
     */
    private function compileVoltTemplates()
    {
        $path = $this->config->path;
        $devPath = $this->config->dev->path;

        $voltCacheDirBak = $path->voltCacheDir;
        $voltCacheDirBuild = $devPath->prodDir . "cache-static/volt/";
        $path->voltCacheDir = $voltCacheDirBuild;
        echo "Temporarily changing voltCacheDir to {$voltCacheDirBuild}\n";

        try {
            $this->compileVoltTemplateForModule('admin');
            $this->compileVoltTemplateForModule('web');
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        // Simple views
        $this->compileVoltDir($path->viewsSimpleDir, function() {
            return $this->getViewSimple();
        });

        // Common partial views
        // This is a bit hacky but it works.  We are treating the common partials as Simple to compile them.
        // We are simply changing the directory to look into and on the view itself
        $this->compileVoltDir($path->viewsCommonDir . 'partials/', function() {
            $config = $this->getConfig();
            $simpleView = $this->getViewSimple();
            $simpleView->setViewsDir($config->path->viewsCommonDir . 'partials/');
            return $simpleView;
        });

        $path->voltCacheDir = $voltCacheDirBak;
        echo "Reverting voltCacheDir to original path\n";
    }

    /**
     *
     */
    private function compileVoltTemplateForModule($moduleName)
    {
        $moduleClass = '\\Webird\\Modules\\' . ucfirst($moduleName) . '\\Module';
        $module = new $moduleClass();

        $this->compileVoltDir($module->getViewsDir(), $module->getViewFunc());
    }

    /**
     *
     */
    private function compileVoltDir($path, $viewFunc)
    {
        $this->compileVoltDirWorker($path, \Closure::bind($viewFunc, $this->getDI()));
    }

    /**
     *
     */
    private function compileVoltDirWorker($path, $viewFunc)
    {
        $dh = opendir($path);
        while (($fileName = readdir($dh)) !== false) {
            if ($fileName == '.' || $fileName == '..') {
                continue;
            }

            $pathNext = "{$path}{$fileName}";
            if (is_dir($pathNext)) {
                $this->compileVoltDirWorker("$pathNext/", $viewFunc);
            } else {
                $this->getDI()
                    ->getVoltShared($viewFunc())
                    ->getCompiler()
                    ->compile($pathNext);
            }
        }

        // close the directory handle
        closedir($dh);
    }

    /**
     *
     */
    private function makeEntryPoints()
    {
        $prodDir = $this->config->dev->path->prodDir;

        // Create CLI bootstrap entry
        $cliEntry = <<<'WEBIRD_ENTRY'
#!/usr/bin/env php
<?php
define('ENV', 'prod');
require __DIR__ . '/phalcon/bootstrap_cli.php';
WEBIRD_ENTRY;
        file_put_contents("$prodDir/run", $cliEntry);
        chmod("$prodDir/run", 0775);

        // Create web bootstrap entry
        $webEntry = <<<'WEBIRD_ENTRY'
<?php
define('ENV', 'prod');
require __DIR__ . '/../phalcon/bootstrap_web.php';
WEBIRD_ENTRY;
        file_put_contents("$prodDir/public/index.php", $webEntry);
    }

    /**
     *
     */
    private function copyFiles()
    {
        $projectDir = $this->config->dev->path->projectDir;
        $appDir = $this->config->path->appDir;
        $etcDir = $this->config->dev->path->etcDir;
        $devDir = $this->config->dev->path->devDir;
        $prodDir = $this->config->dev->path->prodDir;

        // Copy Composer configuration
        copy("$projectDir/composer.json", $prodDir . 'composer.json');
        copy("$projectDir/composer.lock", $prodDir . 'composer.lock');

        // Copy NPM configuration
        copy("$projectDir/package.json", $prodDir . 'package.json');
        copy("$projectDir/package-lock.json", $prodDir . 'package-lock.json');

        `cp -R $appDir/theme/assets {$prodDir}public/assets`;
        `cp -R $appDir/static {$prodDir}public/static`;

        copy("$etcDir/schema.sql", $prodDir . 'etc/schema.sql');
    }

    /**
     *
     */
    private function buildConf()
    {
        $etcDir = $this->config->dev->path->etcDir;
        $localeDir = $this->config->path->localeDir;
        $devDir = $this->config->dev->path->devDir;
        $prodDir = $this->config->dev->path->prodDir;

        $config1 = json_decode(file_get_contents($etcDir . 'prod_defaults.json'), true);
        $config2 = json_decode(file_get_contents($etcDir . 'prod.json'), true);

        $localeConfig = json_decode(file_get_contents($localeDir . 'config.json'), true);
        $localeConfig['supported'] = $this->getDI()
            ->getLocale()
            ->getSupportedLocales();
        $config3 = [
            'locale' => $localeConfig
        ];

        // Merge the custom settings over the defaults
        $configMerged = array_replace_recursive($config1, $config2, $config3);

        // Write the merged settings to the build directory
        file_put_contents("$prodDir/etc/config.json", json_encode($configMerged));
    }

    /**
     *
     */
    private function installPackages()
    {
        $prodDir = $this->config->dev->path->prodDir;

        $cwd = getcwd();
        chdir($prodDir);

        exec("composer --no-dev install", $out, $ret);
        exec("skipclean=1 && npm install --production", $out, $ret);

        chdir($cwd);
    }

    /**
     *
     */
    private function compileLocales()
    {
        $localeCacheDir = $this->config->dev->path->prodDir . 'cache-static/locale/';

        $supported = $this->getDI()
            ->getLocale()
            ->getSupportedLocales();

        foreach ($supported as $locale => $true) {
            try {
                $compiler = new LocaleCompiler();
                $success = $compiler->compileLocale([
                    'locale'         => $locale,
                    'domains'        => $this->config->locale->domains,
                    'localeDir'      => $this->config->path->localeDir,
                    'localeCacheDir' => $localeCacheDir,
                ]);
            } catch (LocaleCompilerException $e) {
                error_log($e->getMessage());
                exit(1);
            }
        }
    }

    /**
     *
     */
    private function buildWebpack()
    {
        $devDirEsc = escapeshellarg($this->config->dev->path->devDir);

        echo "Building webpack bundle.  This usually takes 5-30 seconds and up to 60 seconds on a thin VPS slice.\n";
        echo "Remove unnecessary entry points and dependencies in your webpack config to improve the build performance.\n";
        exec("cd $devDirEsc && npm run build", $out, $ret);
        if ($ret != 0) {
            throw new \Exception('Webpack build error.');
        }
    }

}
