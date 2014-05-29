<?php
namespace Lib;

class Notifier 
{
    private static $instance;
    
    private $expireTime = 2000;

    /**
     * Specifies the urgency level (low, normal, critical)
     * 
     * This option belongs notify-send command from ubuntu kernel. (http://help.ubuntu.ru/wiki/notify-osd)
     * 
     * @var string
     */
    private $urgency = 'critical';
    
    /**
     * Specifies an icon filename or stock icon to display.
     * 
     * This option belongs notify-send command from ubuntu kernel. (http://help.ubuntu.ru/wiki/notify-osd)
     * 
     * @var string
     */
    private $logoName = 'error';
    
    /**
     * @return self
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * This function calls ubuntu function notify-send (http://help.ubuntu.ru/wiki/notify-osd)
     * 
     * @param int $errorCount
     * @param int $lastErrorTime
     */
    public function notify($errorCount, $lastErrorTime)
    {
        $command = 'notify-send ';
        
        $command .= $this->getOption() . ' ';
        
        $command .= '"' . $this->getTitle() . '" ';
        
        $command .= '"' . $this->getMessage((int)$errorCount, (int)$lastErrorTime) . '" ';
        
        exec($command);
    }

    /**
     * The function returns the title to alert.
     * 
     * @return string
     */
    private function getTitle()
    {
        return 'Error on CARiD';
    }

    /**
     * The function prepares a message to send to the command notify-send.
     * 
     * @param int $errorCount
     * @param int $lastErrorTime - timestamp (time of the last error)
     * 
     * @return string
     */
    private function getMessage($errorCount, $lastErrorTime)
    {
        $message = 'Error count: ' . $errorCount . "\n";
        
        $ukrTime = $lastErrorTime + 28800;
        
        $message .= 'Last error (Server | Ukraine): ' .  date('H:i', $lastErrorTime) . ' | ' . date('H:i', $ukrTime);
        
        return $message;
    }

    /**
     * The function prepares command line option for command notify-send.
     * 
     * @return string
     */
    private function getOption()
    {
        return '-u ' . $this->urgency . ' -i ' . $this->logoName . ' ' . '-t '. $this->expireTime;
    }

    private function __construct() { }
    
    private function __clone() { }
} 