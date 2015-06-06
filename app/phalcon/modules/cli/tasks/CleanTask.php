<?php
namespace Webird\Cli\Tasks;

use Webird\CLI\Task;

/**
 * Task for maintanance and cleaning
 *
 */
class CleanTask extends Task
{

    /**
     *
     */
    public function mainAction(array $params)
    {
         echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }

    /**
     *
     */
    public function logAction(array $params)
    {
    }

    /**
     *
     */
    public function sessionAction(array $params)
    {
    }

}
