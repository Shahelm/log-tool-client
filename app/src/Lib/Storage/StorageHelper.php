<?php
namespace Lib\Storage;

use Symfony\Component\Console\Helper\Helper;

/**
 * Class StorageHelper
 *
 * @package Lib\Storage
 */
class StorageHelper extends Helper
{
    /**
     * @var \Lib\Storage\Storage
     */
    private $storage;
    
    /**
     * @var string
     */
    private $storageDir;
    
    /**
     * @var string
     */
    private $storageFileName;
    
    public function __construct()
    {
        $this->storageDir = sys_get_temp_dir();
        
        $this->storageFileName = 'log-tool-client';
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        if (null ===$this->storage) {
            $this->storage = new Storage(new FileStorage($this->storageDir, $this->storageFileName));
        }
        
        return $this->storage;
    }
    
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'storageHelper';
    }
}
