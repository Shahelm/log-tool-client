<?php
namespace Lib\Storage;

/**
 * Class FileStorage
 *
 * @package Lib\Storage
 */
class FileStorage implements IStorage
{
    /**
     * @var string
     */
    private $filePath;
    
    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $filePath
     * @param string $fileName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($filePath, $fileName)
    {
        $this->filePath = rtrim($filePath, DIRECTORY_SEPARATOR);
        
        $this->fileName = $fileName;
        
        if (!is_writable($this->filePath)) {
            throw (new \InvalidArgumentException(sprintf('%s must be writable.', $this->filePath)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, $value)
    {
        $data = $this->getDataFromFile();
        
        $data[$key] = $value;
        
        $data = serialize($data);
        
        $return = file_put_contents($this->getFile(), $data);
        
        return is_numeric($return) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = $this->getDataFromFile();
        
        return isset($data[$key]) ? $data[$key] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $data = $this->getDataFromFile();
        
        return isset($data[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, $value)
    {
        $return = false;
        
        $data = $this->getDataFromFile();
        
        if (is_array($data) && isset($data[$key])) {
            $data[$key] = $value;

            $data = serialize($data);

            $return = file_put_contents($this->getFile(), $data);
                
            $return = is_numeric($return) ? true : false;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        $return = false;

        if (file_exists($this->getFile())) {
            $return = unlink($this->getFile());
        }

        return $return;
    }

    /**
     * The function returns the data from the file into an array, or an empty array if no data.
     *
     * @return array
     */
    private function getDataFromFile()
    {
        if (!file_exists($this->getFile())) {
            return array();
        }
        
        $return = array();
        
        $string = file_get_contents($this->getFile());
        
        if (!empty($string)) {
            $return = unserialize($string);
        }
        
        return 0 === count($return) ? $return : array();
    }

    /**
     * @return string
     */
    private function getFile()
    {
        return $this->filePath . DIRECTORY_SEPARATOR . $this->fileName;
    }
}
