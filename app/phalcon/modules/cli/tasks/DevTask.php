<?php
namespace Webird\Modules\Cli\Tasks;

use Phalcon\Mvc\View\Engine\Volt\Compiler as Compiler,
    Phalcon\Mvc\View\Engine\Volt,
    React\EventLoop\Factory as EventLoopFactory,
    Webird\CLI\Process,
    Webird\CLI\Task;

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
                'optional' => []
            ],
            'opts' => []
        ]);

        $devDir = $config->dev->path->devDir;
        $runEsc = escapeshellcmd("$devDir/run");
        $devDirEsc = escapeshellarg($devDir);

        $websocketProc = new Process("$runEsc websocket");
        $webpackProc = new Process("cd $devDirEsc && npm run dev");

        $loop = EventLoopFactory::create();
        $loop->addTimer(0.001, function($timer) use ($websocketProc, $webpackProc) {
            $websocketProc->start($timer->getLoop());
            $websocketProc->addStdListeners();

            $webpackProc->start($timer->getLoop());
            $webpackProc->addStdListeners();
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
        $devDir = $config->dev->path->devDir;
        $etcDir = $config->dev->path->etcDir;
        $webpackPort = $config->dev->webpackPort;
        $httpPort = $config->app->httpPort;
        $websocketPort = $config->app->wsPort;
        $randomHash = uniqid();

        $domainFirst = $config->site->domains[0];
        $domains = $config->site->domains->toArray();

        return $this->getDI()
            ->getViewSimple()
            ->render('nginx/dev', [
                'host'           => $domainFirst,
                'domains'        => $domains,
                'http_port'      => $httpPort,
                'webpack_port'   => $webpackPort,
                'websocket_port' => $websocketPort,
                'random_hash'    => $randomHash,
                'app_path'       => $appDir,
                'dev_path'       => $devDir
        ]);
    }

}
