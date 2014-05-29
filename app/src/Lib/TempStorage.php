<?php
namespace Lib;

class TempStorage
{
    /**
     * @var self reference to singleton instance
     */
    private static $instance;
    
    private $dir;
    
    private $fileName;
    
    /**
     * gets the instance via lazy initialization (created on first usage)
     *
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
     * is not allowed to call from outside: private!
     *
     */
    private function __construct()
    {
        $this->dir = sys_get_temp_dir();
        
        $this->fileName = Config::getInstance()->get('temp-file-name'); 
    }

    public function savePid()
    {
        $pid = getmypid();
        
        if ($pid > 0) {
            $this->write($pid);
        }
    }
    
    public function getPid()
    {
        $handle = $this->openTempFile('r');
        
        $pid = fgets($handle);
        
        return $pid;
    }
   
    public function isWritable()
    {
        return is_writable($this->dir);
    }
    
    public function deleteTempFile()
    {
        if (is_file($this->getFilePath())) {
            unlink($this->getFilePath());
        }
    }
    
    private function write($data)
    {
        $file = $this->openTempFile('w');

        fwrite($file, $data);
        
        fclose($file);
    }

    private function openTempFile($mode)
    {
        return fopen($this->getFilePath(), $mode);
    }
 
    private function getFilePath()
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->fileName;
    }
    
    private function __clone() { }

    private function __wakeup() { }
}

 