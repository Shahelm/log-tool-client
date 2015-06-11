<?php
namespace Lib\Storage;

/**
 * Interface IStorage
 *
 * @package Lib\Storage
 */
interface IStorage
{
    /**
     * Function stores the $value in the repository.
     *
     * @param string $key
     * @param string | int $value
     *
     * @return bool
     */
    public function save($key, $value);

    /**
     * The function returns the data by key or null.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * The function returns true if $ key exists in the storage.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);
    
    /**
     * Function updates the values ​​by key or returns false.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function update($key, $value);

    /**
     * Function clears the storage.
     *
     * @return bool
     */
    public function flushAll();
}
