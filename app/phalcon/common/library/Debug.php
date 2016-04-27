<?php
namespace Webird;

use Phalcon\DI;

/**
 *
 */
class Debug
{
    /**
     *
     */
    public static function log($message)
    {
        DI::getDefault()
            ->get('debug')
            ->log($message);
    }

    /**
     *
     */
    public static function debug($message)
    {
        DI::getDefault()
            ->get('debug')
            ->debug($message);
    }

    /**
     *
     */
    public static function warning($message)
    {
        DI::getDefault()
            ->get('debug')
            ->warning($message);
    }

    /**
     *
     */
    public static function export($message)
    {
        DI::getDefault()
            ->get('debug')
            ->log(var_export($message, true));
    }
}
