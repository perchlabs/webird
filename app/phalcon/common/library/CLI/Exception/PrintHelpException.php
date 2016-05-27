<?php
namespace Webird\CLI\Exception;

use Exception;

/**
 *
 */
class PrintHelpException extends Exception
{
    /**
     *
     */
    private $cmdDef;

    /**
     *
     */
    private $specs;

    /**
     *
     */
    public function getCmdDef()
    {
        return $this->cmdDef;
    }

    /**
     *
     */
    public function getSpecs()
    {
        return $this->specs;
    }

    /**
     *
     */
    public function __construct($cmdDef, $specs, $message = '', $code = 0)
    {
        parent::__construct($message, $code);

        $this->cmdDef = $cmdDef;
        $this->specs = $specs;
    }

}
