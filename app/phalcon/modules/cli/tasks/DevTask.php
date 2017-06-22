<?php
namespace Webird\Modules\Cli\Tasks;

use Phalcon\Mvc\View\Engine\Volt\Compiler as Compiler;
use Phalcon\Mvc\View\Engine\Volt;
use React\EventLoop\Factory as EventLoopFactory;
use Webird\CLI\Process;
use Webird\CLI\Task;

/**
 * Task for Build
 *
 */
class DevTask extends Task
{
    /**
     *
     */
    public function serverAction($argv)
    {
        $config = $this->getDI()
            ->getConfig();

        $help = <<<HELPMSG
* PHP Ratchet websocket server on port {$config->app->wsPort}
* ZMQ server on port {$config->app->zmqPort}
* Node.js/Gulp Webpack build environment server on port {$config->dev->webpackPort}
HELPMSG;

        $params = $this->parseArgs($argv, [
            'title' => "Start the dev (development) server processes",
            'help' => $help,
            'args' => [
                'required' => [],
                'optional' => [],
            ],
            'opts' => [],
        ]);

        $devDir = $config->dev->path->devDir;
        $runEsc = escapeshellcmd($devDir . 'run');
        $devDirEsc = escapeshellarg($devDir);

        $serversentProc = new Process("$runEsc serversent");
        $websocketProc = new Process("$runEsc websocket");
        $webpackProc = new Process("cd $devDirEsc && npm run dev");

        $procs = [$serversentProc, $websocketProc, $webpackProc];

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
            'title' => 'Generate a dev (development) nginx configuration',
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
            ->render('nginx/dev', [
                'config'      => $config,
                'random_hash' => uniqid(),
        ]);
    }

}
