<?php
namespace Webird\Cli\Tasks;

use Webird\Cli\TaskBase;

/**
 * Task for maintanance and cleaning
 *
 */
class CleanTask extends TaskBase
{
    public function mainAction(array $params)
    {
         echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }



    public function logAction(array $params)
    {
    }




    public function sessionAction(array $params)
    {
    }

}
