<?php
namespace Webird\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

/**
 * Security Plugin to be attached to a \Phalcon\Mvc\Dispatcher object
 */
class DispatcherSecurity extends Plugin
{

    /**
     * {@inheritdoc}
     *
     * @param  \Phalcon\Events\Event         $event
     * @param  \Phalcon\Mvc\Dispatcher       $dispatcher
     */
    public function beforeDispatchLoop(Event $event, Dispatcher $dispatcher)
    {
        $https = $this->config->security->https;
        $hsts = $this->config->security->hsts;

        // If HTTPS or HSTS are required.
        if ($https || ($hsts > 0)) {
            // is HTTPS currently active.
            // HTTP_X_FORWARDED_PROTO checks for reverse proxies that that communicate with the server using HTTP
            $isCurrentlyHttps = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'))
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'));

            // At this point HTTPS is required and it redirects to HTTPS if the current connection is not secure
            if (!$isCurrentlyHttps) {
                // Create a new URL. The URL service will be set to generate HTTPS by these same configuration options
                $secureUrl = $this->url->get($_SERVER["REQUEST_URI"], true);
                // Redirect the client to the HTTPS protocol version of this page
                $this->response->redirect($secureUrl, true, 301);
                // Stop entire dispatch operation for redirect
                return false;
            }
            // At this point the client is already connected with the HTTPS protocol.
            // Send HSTS header if set in the configuration
            else if ($hsts > 0) {
                // Note: This feature is currently not supported on any IE as of version 11 (and is quitely ignored).
                $this->response->setHeader('Strict-Transport-Security', 'max-age='.(string)$hsts);
            }
        }

        // Prevents the webpage from being contained within an iframe of a foreign domain
        if ($this->config->security->preventClickjack) {
            $this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param  \Phalcon\Events\Event         $event
     * @param  \Phalcon\Mvc\Dispatcher       $dispatcher
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $translate = $this->getDI()->getTranslate();

        $module = $this->router->getModuleName();
        $moduleDefault = $this->router->getDefaultModule();

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // If the resource is public than allow the action and return true
        if ($this->acl->isPublic($module, $controller, $action)) {
            return true;
        }

        try {
            // If there is no identity available the resource is downgraded until finally it
            // redirects to the index/index of the default module
            if (!$this->auth->hasIdentity()) {
                $this->flash->notice($translate->gettext("You don't have access to the restricted resource"));
                if ($this->acl->isPublic($module, $controller, 'index')) {
                    return $this->stopAndForwardModuleSafe($module, $controller, 'index', $dispatcher);
                } else if ($this->acl->isPublic($module, 'index', 'index')) {
                    return $this->stopAndForwardModuleSafe($module, 'index', 'index', $dispatcher);
                } else {
                    return $this->stopAndForwardModuleSafe($moduleDefault, 'index', 'index', $dispatcher);
                }
            }
        } catch (\Exception $e) {
            error_log('Security Error: ' . $e->getMessage());
            return false;
        }

        // If the auth system requires the user password be reset then force this action
        // by canceling anything but the change password action. This redirects to prevent
        // double POSTing from a signin action to the change password action.
        if ($this->auth->doesNeedToChangePassword()) {
            if ("web:settings.changePassword" != "{$module}:{$controller}.{$action}") {
                $this->getDI()->getResponse()->redirect('settings/changePassword');
                return false;
            }
        }

        try {
            $role = $this->auth->getRole();
            // Check if the user has permission and attempts to downgrade the resource
            // until it finally gives up and redirects to the index/index of the default module
            if (!$this->acl->isAllowed($role, $module, $controller, $action)) {
                $this->flash->notice($translate->gettext('You do not have access to the resource'));
                if ($this->acl->isAllowed($role, $module, $controller, 'index')) {
                    return $this->stopAndForwardModuleSafe($module, $controller, 'index', $dispatcher);
                } else if ($this->acl->isAllowed($role, $module, $controller, 'index')) {
                    return $this->stopAndForwardModuleSafe($module, 'index', 'index', $dispatcher);
                } else {
                    return $this->stopAndForwardModuleSafe($moduleDefault, 'index', 'index', $dispatcher);
                }
            }
        } catch (\Exception $e) {
            error_log('Security Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Forwards if the current module is the same as the destination module and redirects
     * if they are different.  In the case of a redirect it cleans up the path for the
     * default module and for index/index and :controller/index
     *
     * @param string $moduleDestination
     * @param string $controller
     * @param string $action
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     * @return boolean
     */
    private function stopAndForwardModuleSafe($moduleDestination, $controller, $action, $dispatcher)
    {
        $moduleCurrent = $this->router->getModuleName();
        $moduleDefault = $this->router->getDefaultModule();

        if ($moduleCurrent == $moduleDestination) {
            $dispatcher->forward([
                'controller' => $controller,
                'action' => $action
            ]);
        } else {
            $pathArr = [];
            if ($moduleDestination != $moduleDefault) {
                $pathArr[] = $moduleDestination;
            }

            if ($controller == 'index' && $action == 'index') {
                // Do nothing to prevent ugly index/index
            } else if ($action == 'index') {
                $pathArr[] = $controller;
            } else {
                $pathArr[] = $controller;
                $pathArr[] = $action;
            }
            $path = implode('/', $pathArr);

            $this->response->redirect($path);
            $this->response->send();
        }

        return false;
    }

}
