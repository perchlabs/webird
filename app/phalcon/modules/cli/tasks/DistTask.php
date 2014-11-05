<?php
namespace Webird\Cli\Tasks;

use React\EventLoop\Factory as EventLoopFactory,
    React\ChildProcess\Process,
    Webird\Cli\TaskBase;

/**
 *
 *
 */
class DistTask extends TaskBase
{

    public function mainAction(array $params)
    {
    }




    public function serverAction(array $params)
    {
        $appDir = $this->config->path->appDir;
        $cmdWebirdEsc = escapeshellcmd("$appDir/webird.php");

        $websocketProc = new Process("$cmdWebirdEsc websocket");

        $loop = EventLoopFactory::create();
        $loop->addTimer(0.001, function($timer) use ($websocketProc) {
            $websocketProc->start($timer->getLoop());
            $websocketProc->stdout->on('data', function($output) {
                echo $output;
            });
            $websocketProc->stderr->on('data', function($output) {
                echo $output;
            });
        });

        $loop->run();
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
        $httpPort = $config->app->httpPort;
        $wsPort = $config->app->wsPort;
        $randomHash = uniqid();

        $domainFirst = $config->site->domains[0];

        $template = $this->di->get('template');
        $tpl = $template->render('nginx/dist', [
            'host'           => $domainFirst,
            'domains'        => $config->site->domains,
            'http_port'      => $httpPort,
            'websocket_port' => $wsPort,
            'random_hash'    => $randomHash,
            'app_path'       => $appDir,
        ]);

        return $tpl;
    }


}
