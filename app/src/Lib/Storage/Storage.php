<?php
namespace Lib\Storage;

class Storage 
{
    /**
     * @var IStorage
     */
    private $storage;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Function stores the $value in the repository.
     *
     * @param string $key
     * @param string | int $value
     *
     * @return bool
     */
    public function save($key, $value)
    {
        return $this->storage->save($key, $value);
    }

    /**
     * The function returns the data by key or null.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->storage->get($key);
    }

    /**
     * The function returns true if $ key exists in the storage.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->storage->has($key);
    }

    /**
     * Function updates the values ​​by key or returns false.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function update($key, $value)
    {
        return $this->storage->update($key, $value);
    }

    /**
     * Function clears the storage.
     *
     * @return bool
     */
    public function flushAll()
    {
        return $this->storage->flushAll();
    }
} 