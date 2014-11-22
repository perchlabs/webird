<?php
namespace Webird\Mvc\View;

use Phalcon\Mvc\View\Simple as PhViewSimple;

/**
 * Simple View
 */
class Simple extends PhViewSimple
{
    /**
     * Constructor
     *
     * @param \Phalcon\DI   $di
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function render($path, $params = null)
    {
        $config = $this->getDI()->get('config');

        $this->setVars([
            'DEV'    => DEV,
            'TEST'   => (TEST_ENV === ENV),
            'DIST'   => (DIST_ENV === ENV),
            'domain' => $config->server->domain,
            'link'   => $config->site->link
        ]);

        return parent::render($path, $params);
    }

}
