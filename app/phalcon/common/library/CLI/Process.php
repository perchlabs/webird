<?php
namespace Webird\CLI;

use React\ChildProcess\Process as ReactProcess;

/**
 * Issue commands
 *
 */
class Process extends ReactProcess
{
    /**
    * Add IO listeners
    *
    */
    public function addStdListeners()
    {
        $this->stdout->on('data', function($output) {
            fwrite(STDOUT, $output);
        });
        $this->stderr->on('data', function($output) {
            fwrite(STDERR, $output);
        });

        return $this;
    }
}
