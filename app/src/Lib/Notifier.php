<?php
namespace Lib;

/**
 * Class Notifier
 *
 * @package Lib
 */
class Notifier
{
    /**
     * in milliseconds
     *
     * @var int
     */
    private $expireTime;

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

    public function __construct($expireTime)
    {
        $this->expireTime = $expireTime;
    }
    
    /**
     * This function calls ubuntu function notify-send (http://help.ubuntu.ru/wiki/notify-osd)
     *
     * @param int $errorCount
     * @param int $numberOfErrorsForFiveMinutes
     * @param int $lastErrorTime
     */
    public function notify($errorCount, $numberOfErrorsForFiveMinutes, $lastErrorTime)
    {
        $command = 'notify-send ';
        
        $command .= $this->getOption() . ' ';
        
        $command .= '"' . $this->getTitle() . '" ';
        
        $command .= '"' . $this->getMessage(
            (int)$errorCount,
            (int)$numberOfErrorsForFiveMinutes,
            (int)$lastErrorTime
        ) . '" ';
        
        exec($command);
    }

    /**
     * @return void
     */
    public function notifyServicesUnavailable()
    {
        $command = 'notify-send' . ' ' . $this->getOption() . ' ' . 'Log tool unavailable!';

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
     * @param int $errorLastMinutes
     * @param int $errorsFiveMinutes
     * @param int $lastErrorTime - timestamp (time of the last error)
     *
     * @return string
     */
    private function getMessage($errorLastMinutes, $errorsFiveMinutes, $lastErrorTime)
    {
        $message = 'Error last minutes: ' . $errorLastMinutes . "\n";
        $message .= 'Error last five minutes: ' . $errorsFiveMinutes . "\n";
        
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
}
