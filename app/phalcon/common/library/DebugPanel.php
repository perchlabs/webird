<?php
namespace Webird;

use Phalcon\Db\Profiler as Profiler,
		Phalcon\DI\Injectable as DIInjectable;

class DebugPanel extends DIInjectable
{
		private $startTime,
						$endTime,
						$queryCount;

		protected $profiler,
							$viewsRendered,
			  			$serviceNames;

		public function __construct($di)
		{
error_log('HERE');

				$eventsManager = $di->get('eventsManager');

				$serviceNames = [
						'db'       => ['db'],
						'dispatch' => ['dispatcher'],
						'view'     => ['view']
				];

				$this->queryCount = 0;
 				$this->viewsRendered = [];
				$this->serviceNames = [];

				$this->startTime = microtime(true);
				$this->profiler = new Profiler();

				foreach ($di->getServices() as $service) {
						$name = $service->getName();
						foreach ($serviceNames as $eventName => $services) {
								if (in_array($name, $services)) {
										$service->setShared(true);
										$di->get($name)->setEventsManager($eventsManager);
				            break;
								}
						}
				}
				foreach (array_keys($serviceNames) as $eventName) {
						$eventsManager->attach($eventName, $this);
				}
				$this->serviceNames = $serviceNames;
		}

		public function getServices($event)
		{
				return $this->serviceNames[$event];
		}

		public function beforeQuery($event, $connection)
		{
				$this->profiler->startProfile(
						$connection->getRealSQLStatement(),
						$connection->getSQLVariables(),
						$connection->getSQLBindTypes()
				);
		}

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
				$router = $this->getDI()->getRouter();
				$phalconDir = $this->getDI()->getConfig()->path->phalconDir;

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
						'module'     => $router->getModuleName(),
						'controller' => $view->getControllerName(),
						'action'     => $view->getActionName()
				];
		}

		public function afterRender($event, $view, $viewFile)
		{
				$this->endTime = microtime(true);

				$debugPanel = $this->renderPanel();
				$content = $view->getContent();

				$newContent = str_replace('<!--DEBUG_PANEL-->', $debugPanel, $content, $count);
				if ($count > 0) {
						$view->setContent($newContent);
				}
		}

		public function renderPanel()
		{
				$panels = [
						'server'   => $this->getServerPanel(),
						'request'  => $this->getRequestPanel(),
						'database' => $this->getDbPanel(),
						'views'		 => $this->getViewsPanel(),
						'config'   => $this->getConfigPanel()
				];

				$content = $this->getView()->render("debug_panel/index", [
						'panels' => $panels,
						'loadTime' => round(($this->getEndTime() - $this->getStartTime()), 6),
						'elapsedTime' => round(($this->getEndTime() - $_SERVER['REQUEST_TIME'] ), 6),
						'mem' => number_format(memory_get_usage()/1024, 2),
						'memPeak' => number_format(memory_get_peak_usage()/1024, 2),
						'sessionSize' => (isset($_SESSION)) ? mb_strlen(serialize($_SESSION)/1024) : 0
				]);

				return $content;
		}


		public function getServerPanel()
		{
				$view = $this->getView();
				$view->render("debug_panel/panels/server", [
						'SERVER' => $_SERVER,
						'headersList' => headers_list()
				]);
				return $view;
		}


		public function getRequestPanel()
		{
				$session = [];
				if (isset($_SESSION)) {
					foreach ($_SESSION as $key => $value) {
							$session[$key] = var_export($value, true);
					}
				}

				$view = $this->getView();
				$view->render("debug_panel/panels/request", [
						'SESSION' => $session,
						'COOKIE'  => $_COOKIE,
						'GET'			=> $_GET,
						'POST'    => $_POST,
						'FILES'		=> $_FILES
				]);
				return $view;
		}


		public function getDbPanel()
		{
				$dbProfiles = $this->getProfiler()->getProfiles();
				if (!isset($dbProfiles)) {
						$dbProfiles = [];
				}

				$profiles = [];
				foreach ($dbProfiles as $profile) {
						$profiles[] = [
								'time' => $profile->getTotalElapsedSeconds(),
								'sql'  => $profile->getSQLStatement(),
								'vars' => $profile->getSQLVariables()
						];
				}

				$dbs = [];
				foreach ($this->getServices('db') as $dbName) {
						$db = $this->getDI()->get($dbName);
						$dbs[$dbName] = $db;
				}

				$view = $this->getView();
				$view->render("debug_panel/panels/db", [
					  'profiles'  => $profiles,
						'dbs'       => $dbs
				]);
				return $view;
		}

		public function getViewsPanel()
		{
				$view = $this->getView();
				$view->render("debug_panel/panels/views", [
						'viewsRendered' => $this->getRenderedViews()
				]);
				return $view;
		}


		public static function object_to_array($d)
		{
        if (is_object($d)) {
            $d = get_object_vars($d);
				}

        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }


		public function getConfigPanel()
		{
				$config = $this->getDI()->getConfig();

				$view = $this->getView();
				$view->render("debug_panel/panels/config", [
						'config' => self::object_to_array($config),
				]);
				return $view;
		}

		protected function getView()
		{
				$view = $this->getDI()->get('viewSimple');
				return $view;
		}

		public function getStartTime()
		{
				return $this->startTime;
		}

		public function getEndTime()
		{
				return $this->endTime;
		}

		public function getRenderedViews()
		{
				return $this->viewsRendered;
		}

		public function getQueryCount()
		{
				return $this->queryCount;
		}

		public function getProfiler()
		{
				return $this->profiler;
		}
}
