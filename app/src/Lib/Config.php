<?php
namespace Lib;

/**
 * Class Config
 *
 * @package Lib
 */
class Config
{
    private $separator = '.';

    /**
     * @var array
     */
    private $config;
    
    /**
     * @var Config
     */
    protected static $instance;

    /**
     * Gets the instance via lazy initialization (created on first usage)
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
    
    protected function __construct()
    {
        $this->config = require_once __DIR__ . '/../../config.php';
    }

    /**
     * Function return value by key.
     * If the key is present in the '.' it means to get a child section.
     *
     * Example config:
     * return array(
     *    'key' => array('child-key' => value),
     * );
     *
     * Example of using:
     * Config::getInstance()->get('key.child-key') - return value for child-key
     *
     * @param string $queryString
     *
     * @return array|string
     *
     * @throws \InvalidArgumentException
     */
    public function get($queryString)
    {
        if ('' === $queryString) {
            throw new \InvalidArgumentException('Query string can not be empty.');
        }

        if (!is_string($queryString)) {
            throw new \InvalidArgumentException('Query string must be a string.');
        }

        $keys = $this->parseQueryString($queryString);

        $count = count($keys);

        $return = array();

        if ($count > 0) {
            $config = $this->config;

            foreach ($keys as $key) {
                if ($this->hasKey($config, $key)) {
                    $return = $this->getKey($config, $key);
                    $config = $return;
                } else {
                    throw new \InvalidArgumentException(sprintf('The key: %s is not in the configuration file.', $key));
                }
            }
        }

        return $return;
    }

    /**
     * Function return config file as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

    /**
     * @param string $queryString
     *
     * @return array
     */
    private function parseQueryString($queryString)
    {
        return explode($this->separator, $queryString);
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @return array|string
     */
    private function getKey($config, $key)
    {
        return $config[$key];
    }

    /**
     * @param array $config
     * @param string $key
     *
     * @return bool
     */
    private function hasKey($config, $key)
    {
        return isset($config[$key]);
    }
}
