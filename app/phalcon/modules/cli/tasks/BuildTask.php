<?php
namespace Webird\Modules\Cli\Tasks;

use Phalcon\Mvc\View\Engine\Volt\Compiler as Compiler,
    Phalcon\Mvc\View\Engine\Volt,
    React\EventLoop\Factory as EventLoopFactory,
    React\ChildProcess\Process,
    Webird\Modules\Web\Module as WebModule,
    Webird\Modules\Admin\Module as AdminModule,
    Webird\CLI\Task,
    Webird\Locale\Compiler as LocaleCompiler,
    Webird\Locale\CompilerException as LocaleCompilerException;

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
                'optional' => []
            ],
            'opts' => []
        ]);

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

        exit(0);
    }

    /**
     *
     */
    private function buildPhalconDir()
    {
        $config = $this->config;
        $phalconDir = $config->path->phalconDir;
        $buildDir = $config->dev->path->buildDir;

        $buildDirEsc = escapeshellarg($buildDir);
        $phalconAppDirEsc = escapeshellarg($phalconDir);
        $phalconBuildDirEsc = escapeshellarg($buildDir . 'phalcon');

        if (! isset($config['dev']['phpEncode'])) {
            throw new \Exception('The PHP Encoder value is not set.', 1);
        }
        $phpEncode = $config->dev->phpEncode;

        if (empty($phpEncode)) {
            `cp -R $phalconAppDirEsc $phalconBuildDirEsc`;
        } else {
            if (! isset($config->dev->phpEncoders[$phpEncode])) {
                throw new \Exception("The '$phpEncode' PHP encoder setting does not exist", 1);
            }

            $encoder = $config->dev->phpEncoders[$phpEncode];
            $encCmdEsc = escapeshellcmd($encoder->path);
            switch ($phpEncode) {
                case 'ioncube':
                    $cmd = "$encCmdEsc $phalconAppDirEsc --into $buildDirEsc --merge-target";
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
        $buildDir = $this->config->dev->path->buildDir;
        $buildDirEsc = escapeshellarg($buildDir);

        // TODO: Add more checks for disasters against the rm -Rf command
        // Check for some disaster cases since the script will try to recursively delete the folder
        if ($buildDir != "{$projectDir}build/" || $buildDir == '' || $buildDir == '/') {
            throw new \Exception('Critical Error: Attempting to delete build directory when it is not set correctly.');
        }
        if (file_exists($buildDir)) {
            exec("rm -Rf $buildDirEsc", $out, $ret);
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
        $buildDir = $this->config->dev->path->buildDir;

        mkdir($buildDir);
        mkdir($buildDir . 'public/');
        mkdir($buildDir . 'etc/');
        mkdir($buildDir . 'cache-static/');
        mkdir($buildDir . 'cache-static/volt/');
    }

    /**
     *
     */
    private function compileVoltTemplates()
    {
        $path = $this->config->path;
        $devPath = $this->config->dev->path;

        $voltCacheDirBak = $path->voltCacheDir;
        $voltCacheDirBuild = $devPath->buildDir . "cache-static/volt/";
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
                    ->getVoltService($viewFunc(), $this->getDI())
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
        $buildDir = $this->config->dev->path->buildDir;

        $cliEntry = <<<'WEBIRD_ENTRY'
#!/usr/bin/env php
<?php
define('ENV', 'dist');
require(__DIR__ . '/phalcon/bootstrap_cli.php');
WEBIRD_ENTRY;
        file_put_contents("$buildDir/run", $cliEntry);
        chmod("$buildDir/run", 0775);

        $webEntry = <<<'WEBIRD_ENTRY'
<?php
define('ENV', 'dist');
require(__DIR__ . '/../phalcon/bootstrap_web.php');
WEBIRD_ENTRY;
        file_put_contents("$buildDir/public/index.php", $webEntry);
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
        $buildDir = $this->config->dev->path->buildDir;
        // shell escaped configuration directories
        $appDirEsc = escapeshellarg($appDir);
        $projectDirEsc = escapeshellarg($projectDir);
        $devDirEsc = escapeshellarg($devDir);
        $buildDirEsc = escapeshellarg($buildDir);

        // Copy Composer configuration
        copy("$devDir/composer.json", "$buildDir/composer.json");
        copy("$devDir/composer.lock", "$buildDir/composer.lock");
        // Copy Npm/Nodejs configuration
        copy("$devDir/package.json", "$buildDir/package.json");
        // Copy Bower configuration
        copy("$devDir/bower.json", "$buildDir/bower.json");

        `cp -R $appDir/theme/assets $buildDir/public/assets`;

        copy("$etcDir/schema.sql", "$buildDir/etc/schema.sql");
    }

    /**
     *
     */
    private function buildConf()
    {
        $etcDir = $this->config->dev->path->etcDir;
        $localeDir = $this->config->path->localeDir;
        $devDir = $this->config->dev->path->devDir;
        $buildDir = $this->config->dev->path->buildDir;

        $config1 = yaml_parse_file($etcDir . 'dist_defaults.yml');
        $config2 = yaml_parse_file($etcDir . 'dist.yml');

        $localeConfig = yaml_parse_file($localeDir . 'config.yml');
        $localeConfig['supported'] = $this->getDI()
            ->getLocale()
            ->getSupportedLocales();
        $config3 = [
            'locale' => $localeConfig
        ];

        // Merge the custom settings over the defaults
        $configMerged = array_replace_recursive($config1, $config2, $config3);

        // Write the merged settings to the build directory
        yaml_emit_file("$buildDir/etc/config.yml", $configMerged);
    }

    /**
     *
     */
    private function installPackages()
    {
        $buildDir = $this->config->dev->path->buildDir;

        $cwd = getcwd();
        chdir($buildDir);

        exec("composer --no-dev install", $out, $ret);
        exec("skipclean=1 && npm install --production", $out, $ret);
        exec("bower install --production --allow-root --config.interactive=false", $out, $ret);

        chdir($cwd);
    }

    /**
     *
     */
    private function compileLocales()
    {
        $localeCacheDir = $this->config->dev->path->buildDir . 'cache-static/locale/';

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
                    'localeCacheDir' => $localeCacheDir
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
