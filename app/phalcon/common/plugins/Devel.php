<?php
namespace Webird\Plugins;

use Phalcon\Db\Profiler as Profiler;
use Phalcon\DI\Injectable as DIInjectable;
use Phalcon\Assets\Inline\Js as InlineJs;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

/**
 *
 */
class Devel extends Plugin
{
    /**
     *
     */
    private $startTime;

    /**
     *
     */
    private $queryCount;

    /**
     *
     */
    protected $profiler;

    /**
     *
     */
    private $viewsRendered;

    /**
     *
     */
    public function __construct()
    {
        $this->startTime = microtime(true);

        $this->queryCount = 0;
        $this->viewsRendered = [];

        $this->profiler = new Profiler();
    }

    /**
     *
     */
    public function beforeQuery($event, $connection)
    {
        $this->profiler->startProfile(
            $connection->getRealSQLStatement(),
            $connection->getSQLVariables(),
            $connection->getSQLBindTypes()
        );
    }

    /**
     *
     */
    public function afterQuery($event, $connection)
    {
        $this->profiler->stopProfile();
        $this->queryCount++;
    }

    /**
     * Gets/Saves information about views and stores truncated viewParams.
     *
     * @param unknown $event
     * @param unknown $view
     * @param unknown $file
     */
    public function beforeRenderView($event, $view, $file)
    {
        $router = $this->getDI()
            ->getRouter();
        $config = $this->getDI()
            ->getConfig();
        $phalconDir = $config->path->phalconDir;

        $params = [];
        $toView = $view->getParamsToView();
        $toView = !$toView? [] : $toView;
        foreach ($toView as $k=>$v) {
            if (is_object($v)) {
                $params[$k] = get_class($v);
            } elseif(is_array($v)) {
                $array = [];
                foreach ($v as $key=>$value) {
                    if (is_object($value)) {
                        $array[$key] = get_class($value);
                    } elseif (is_array($value)) {
                        $array[$key] = 'Array[...]';
                    } else {
                        $array[$key] = $value;
                    }
                }
                $params[$k] = $array;
            } else {
                $params[$k] = (string)$v;
            }
        }

        $path = str_replace($phalconDir, '', $view->getActiveRenderPath());
        $path = preg_replace('/^modules\/[a-z]+\/views\/..\/..\/..\//', '', $path);
        $this->viewsRendered[] = [
            'path'       => $path,
            'params'     => $params,
        ];
    }

    /**
     *
     */
    public function getData()
    {
        $endTime = microtime(true);

        return [
            'panels' => [
                'server'   => $this->getServerData(),
                'request'  => $this->getRequestData(),
                'database' => $this->getDbData(),
                'views'    => $this->getViewsData(),
                'config'   => $this->getConfigData(),
            ],
            'measurement' => [
              'loadTime'    => round(($endTime - $this->startTime), 6),
              'elapsedTime' => round(($endTime - $_SERVER['REQUEST_TIME'] ), 6),
              'mem'         => number_format(memory_get_usage()/1024, 2),
              'memPeak'     => number_format(memory_get_peak_usage()/1024, 2),
              'sessionSize' => (isset($_SESSION)) ? mb_strlen(serialize($_SESSION)/1024) : 0,
            ],
        ];
    }

    /**
     *
     */
    public function getServerData()
    {
        return [
            'SERVER' => $_SERVER,
            'headersList' => headers_list(),
        ];
    }

    /**
     *
     */
    public function getRequestData()
    {
        $session = [];
        if (isset($_SESSION)) {
          foreach ($_SESSION as $key => $value) {
              $session[$key] = var_export($value, true);
          }
        }

        return [
            'SESSION' => $session,
            'COOKIE'  => $_COOKIE,
            'GET'     => $_GET,
            'POST'    => $_POST,
            'FILES'   => $_FILES,
        ];
    }

    /**
     *
     */
    public function getDbData()
    {
        $profiles = [];
        foreach ($this->profiler->getProfiles() as $profile) {
            $profiles[] = [
                'time' => $profile->getTotalElapsedSeconds(),
                'sql'  => $profile->getSQLStatement(),
                'vars' => $profile->getSQLVariables(),
            ];
        }

        return [
            'profiles' => $profiles,
        ];
    }

    /**
     *
     */
    public function getViewsData()
    {
        return [
            'viewsRendered' => $this->getRenderedViews(),
        ];
    }

    /**
     *
     */
    public function getConfigData()
    {
        return $this->getDI()
            ->getConfig()
            ->toArray();
    }

    /**
     *
     */
    public function getRenderedViews()
    {
        return $this->viewsRendered;
    }
}
