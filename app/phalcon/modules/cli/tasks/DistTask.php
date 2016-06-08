<?php
namespace Webird\Cli\Tasks;

use React\EventLoop\Factory as EventLoopFactory,
    React\ChildProcess\Process,
    Webird\CLI\Task;

/**
 *
 */
class DistTask extends Task
{

    /**
     *
     */
    public function mainAction(array $params)
    {
    }

    /**
     *
     */
    public function serverAction($argv)
    {
        $config = $this->config;

        $help = <<<HELPMSG
* PHP Ratchet websocket server on port {$config->app->wsPort}
* ZMQ server on port {$config->app->zmqPort}
HELPMSG;

        $params = $this->parseArgs($argv, [
            'title' => 'Start the built server processes.',
            'help' => $help,
            'args' => [
                'required' => [],
                'optional' => []
            ],
            'opts' => [
                'w|wsport:'    => "websockets listen on port (default is {$config->app->wsPort}).",
                'z|zmqport:' => "zmq listen on port (default is {$config->app->zmqPort})."
            ]
        ]);

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

    /**
     *
     */
    public function nginxAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Generate a dist (distribution/production) nginx configuration',
            'args' => [
                'required' => [],
                'optional' => []
            ],
            'opts' => []
        ]);

        $nginxConf = $this->getNginxConfig();
        echo $nginxConf;
    }

    /**
     *
     */
    private function getNginxConfig()
    {
        $config = $this->config;
        $appDir = $config->path->appDir;
        $httpPort = $config->app->httpPort;
        $wsPort = $config->app->wsPort;
        $randomHash = uniqid();

        $domainFirst = $config->site->domains[0];
        $domains = $config->site->domains->toArray();

        $view = $this->di->get('viewSimple');
        $tpl = $view->render('nginx/dist', [
            'host'           => $domainFirst,
            'domains'        => $domains,
            'http_port'      => $httpPort,
            'websocket_port' => $wsPort,
            'random_hash'    => $randomHash,
            'app_path'       => $appDir,
        ]);

        return $tpl;
    }

}
