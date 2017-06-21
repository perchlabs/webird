<?php
namespace Webird\Modules\Cli\Tasks;

use React\EventLoop\Factory as EventLoopFactory;
use Webird\CLI\Process;
use Webird\CLI\Task;

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
                'optional' => [],
            ],
            'opts' => [
                'w|wsport:'    => "websockets listen on port (default is {$config->app->wsPort}).",
                'z|zmqport:' => "zmq listen on port (default is {$config->app->zmqPort}).",
            ],
        ]);

        $appDir = $this->config->path->appDir;
        $runEsc = escapeshellcmd("$appDir/run");

        $serversentProc = new Process("$runEsc serversent");
        $websocketProc = new Process("$runEsc websocket");

        $procs = [$serversentProc, $websocketProc];

        $loop = EventLoopFactory::create();
        $loop->addTimer(0.001, function($timer) use ($procs) {
            foreach ($procs as $proc) {
                $proc->start($timer->getLoop());
                $proc->addStdListeners();
            }
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
                'optional' => [],
            ],
            'opts' => [],
        ]);

        echo $this->getNginxConfig();
    }

    /**
     *
     */
    private function getNginxConfig()
    {
        $config = $this->getDI()
            ->getConfig();

        return $this->getDI()
            ->getViewSimple()
            ->render('nginx/dist', [
                'config'      => $config,
                'random_hash' => uniqid(),
            ]);
    }

}
