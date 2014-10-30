<?php
namespace Webird\Cli\Tasks;

use Phalcon\Mvc\View\Engine\Volt\Compiler as Compiler,
    Phalcon\Mvc\View\Engine\Volt,
    React\EventLoop\Factory as EventLoopFactory,
    React\ChildProcess\Process,
    Webird\Cli\TaskBase,
    Webird\Mvc\ViewBase,
    Webird\Web\Module as WebModule,
    Webird\Admin\Module as AdminModule;

/**
 * Task for Build
 *
 */
class DevTask extends TaskBase
{

    public function mainAction(array $params)
    {
    }




    public function serverAction(array $params)
    {
        $devDir = $this->config->dev->path->devDir;
        $cmdWebirdEsc = escapeshellcmd("$devDir/webird.php");
        $devDirEsc = escapeshellarg($devDir);

        $websocketProc = new Process("$cmdWebirdEsc websocket");
        $webpackProc = new Process("cd $devDirEsc && npm run dev");

        $loop = EventLoopFactory::create();
        $loop->addTimer(0.001, function($timer) use ($websocketProc, $webpackProc) {
            $websocketProc->start($timer->getLoop());
            $this->addProcOutputListener($websocketProc);

            $webpackProc->start($timer->getLoop());
            $this->addProcOutputListener($webpackProc);
        });

        $loop->run();
    }



    private function addProcOutputListener($proc)
    {
        $proc->stdout->on('data', function($output) {
            echo $output;
        });
        $proc->stderr->on('data', function($output) {
            echo $output;
        });
    }




    public function nginxAction(array $params)
    {
        $nginxConf = $this->getNginxConfig();
        echo $nginxConf;
    }






    private function getNginxConfig()
    {
        $config = $this->config;
        $appDir = $config->path->appDir;
        $devDir = $config->dev->path->devDir;
        $etcDir = $config->dev->path->etcDir;
        $domain = $config->site->domain;
        $httpPort = $config->app->httpPort;
        $wsPort = $config->app->wsPort;
        $randomHash = uniqid();

        $inputFile = $etcDir . 'etc/templates/nginx_dev';
        $tpl = file_get_contents($inputFile);

        $tpl = str_replace('{{host}}', $domain, $tpl);
        $tpl = str_replace('{{http_port}}', $httpPort, $tpl);
        $tpl = str_replace('{{websocket_port}}', $wsPort, $tpl);
        $tpl = str_replace('{{random_hash}}', $randomHash, $tpl);
        $tpl = str_replace('{{app_path}}', $appDir, $tpl);
        $tpl = str_replace('{{dev_path}}', $devDir, $tpl);

        return $tpl;
    }

}
