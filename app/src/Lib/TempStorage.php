<?php
namespace Lib;

class TempStorage
{
    /**
     * @var TempStorage
     */
    private static $instance;
    
    /**
     * Path to temp dir in file system.
     * 
     * @var string
     */
    private $dir;
    
    /**
     * Name of the temporary file in the temporary directory.
     * 
     * @var string
     */
    private $fileName;
    
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

    private function __construct()
    {
        $this->dir = sys_get_temp_dir();
        
        $this->fileName = Config::getInstance()->get('temp-file-name'); 
    }

    /**
     * Function save process id in temp file.
     *
     * @return bool
     */
    public function savePid()
    {
        $return = false;
        
        $pid = getmypid();
        
        if ($pid > 0) {
            $return = $this->write($pid);
        }
        
        return !empty($return) ? true : false;
    }

    /**
     * Function return process id client log tool from temp file.
     * 
     * @return string
     */
    public function getPid()
    {
        $handle = $this->openTempFile('r');
        
        $pid = fgets($handle);
        
        return $pid;
    }
   
    /**
     * Function check write permissions for the temporary directory.
     * 
     * @return bool
     */
    public function isWritable()
    {
        return is_writable($this->dir);
    }

    /**
     * Function delete temp file from temp dir.
     *
     * @return bool 
     */
    public function deleteTempFile()
    {
        $return = false;
        
        if (is_file($this->getFilePath())) {
            $return = unlink($this->getFilePath());
        }
        
        return $return;
    }

    /**
     * Function write to temp file process id.
     * 
     * @param int $pid
     *
     * @return bool
     */
    private function write($pid)
    {
        $file = $this->openTempFile('w');

        $return = fwrite($file, $pid);
        
        fclose($file);
        
        return !empty($return) ? true : false;
    }

    /**
     * Function open temp file and return file descriptor.
     * 
     * @param string $mode
     * 
     * @return resource
     */
    private function openTempFile($mode)
    {
        return fopen($this->getFilePath(), $mode);
    }

    /**
     * Function return path for temp file.
     * 
     * @return string
     */
    private function getFilePath()
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->fileName;
    }
    
    private function __clone() { }
}

 