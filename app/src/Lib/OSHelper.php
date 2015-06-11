<?php
namespace Lib;

/**
 * Class OSHelper
 *
 * @package Lib
 */
class OSHelper
{
    /**
     * The function returns the current process id.
     *
     * @return int|bool(false)
     */
    public function getProcessId()
    {
        $pid = getmypid();
        
        return $pid > 0 ? $pid : false;
    }
}
