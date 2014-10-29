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
        $domain = $config->site->domain;
        $httpPort = $config->app->httpPort;
        $wsPort = $config->app->wsPort;
        $randomHash = uniqid();

        $inputFile = "$appDir/etc/nginx_template";
        $tpl = file_get_contents($inputFile);

        $tpl = str_replace('{{host}}', $domain, $tpl);
        $tpl = str_replace('{{http_port}}', $httpPort, $tpl);
        $tpl = str_replace('{{websocket_port}}', $wsPort, $tpl);
        $tpl = str_replace('{{random_hash}}', $randomHash, $tpl);
        $tpl = str_replace('{{app_path}}', $appDir, $tpl);

        return $tpl;
    }


}
