<?php
namespace Lib;

class Notifier 
{
    private static $instance;
    
    private $expireTime = 2000;
    
    private $urgency = 'critical';    
    
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
     * Функция возвращает заголовок оповещения.
     * 
     * @return string
     */
    private function getTitle()
    {
        return 'Error on CARiD';
    }

    /**
     * Функция подготавливает сообщения.
     * 
     * @param int $errorCount - количество ошибок
     * @param int $lastErrorTime - timestamp (время возникновения последней ошибки)
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
     * Функция подготавливает строку опция для команды notify-send.
     * 
     * @return string
     */
    private function getOption()
    {
        return '-u ' . $this->urgency . ' -i ' . $this->logoName . ' ' . '-t '. $this->expireTime;
    }

    private function __construct() { }
    
    private function __clone() { }

    private function __wakeup() { }
} 