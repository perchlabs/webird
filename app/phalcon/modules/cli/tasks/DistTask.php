<?php
namespace Webird\Modules\Cli\Tasks;

use React\EventLoop\Factory as EventLoopFactory,
    Webird\CLI\Process,
    Webird\CLI\Task;

/**
 *
 */
class DistTask extends Task
{
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
        $runEsc = escapeshellcmd("$appDir/run");

        $websocketProc = new Process("$runEsc websocket");

        $loop = EventLoopFactory::create();
        $loop->addTimer(0.001, function($timer) use ($websocketProc) {
            $websocketProc->start($timer->getLoop());
            $websocketProc->addStdListeners();
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

        echo $this->getNginxConfig();
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
        $domains = $config->site->domains;

        return $this->getDI()
            ->getViewSimple()
            ->render('nginx/dist', [
                'host'           => $domainFirst,
                'domains'        => $domains,
                'http_port'      => $httpPort,
                'websocket_port' => $wsPort,
                'random_hash'    => $randomHash,
                'app_path'       => $appDir,
            ]);
    }

}
