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
        $webpackPort = $config->dev->webpackPort;
        $httpPort = $config->app->httpPort;
        $websocketPort = $config->app->wsPort;
        $randomHash = uniqid();

        $domainFirst = $config->site->domains[0];
        $domains = [];
        foreach($config->site->domains as $domain) {
            $domains[] = $domain;
        }
        $domainStr = implode("\n" . str_pad(' ', 14, ' '), $domains);

        $inputFile = $etcDir . 'templates/dev_nginx';
        $tpl = file_get_contents($inputFile);

        $tpl = str_replace('{{host}}', $domainFirst, $tpl);
        $tpl = str_replace('{{domains}}', $domainStr, $tpl);
        $tpl = str_replace('{{http_port}}', $httpPort, $tpl);
        $tpl = str_replace('{{webpack_port}}', $webpackPort, $tpl);
        $tpl = str_replace('{{websocket_port}}', $websocketPort, $tpl);
        $tpl = str_replace('{{random_hash}}', $randomHash, $tpl);
        $tpl = str_replace('{{app_path}}', $appDir, $tpl);
        $tpl = str_replace('{{dev_path}}', $devDir, $tpl);

        return $tpl;
    }

}
