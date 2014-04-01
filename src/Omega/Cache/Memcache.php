<?php

namespace Omega\Cache;

/**
 * Class Memcache
 *
 * @package Omega\Cache
 */
class Memcache implements CacheInterface
{

    /**
     * @var string
     */
    protected $_cachePrefix;
    /**
     * @var int TTL in seconds
     */
    protected $_ttl;

    /**
     * @var \Memcache
     */
    private $_memcache;

    /**
     * @param string         $cachePrefix
     * @param int            $ttl
     * @param \Memcache|null $memcacheObject
     */
    public function __construct(
        $cachePrefix,
        $ttl,
        \Memcache $memcacheObject = null
    )
    {
        if ($memcacheObject === null) {
            $this->_memcache = new \Memcache();
        } else {
            $this->_memcache = $memcacheObject;
        }
    }

    /**
     * Adds new server to pool
     *
     * @param string $host
     * @param int    $port
     * @param bool   $persistent
     * @param int    $weight
     * @param int    $timeout
     * @param int    $retryInterval
     */
    public function addServer(
        $host,
        $port,
        $persistent,
        $weight,
        $timeout,
        $retryInterval
    )
    {
        // Registering server
        $this->_memcache->addserver(
            $host, $port, $persistent, $weight,
            $timeout, $retryInterval
        );
    }

    /**
     * Returns clone of current CacheInterface with
     * adjusted time to live
     *
     * @param int $desiredTtl
     *
     * @return CacheInterface
     */
    public function cloneWithTtl($desiredTtl)
    {
        return new Memcache($this->_cachePrefix, $desiredTtl, $this->_memcache);
    }

    /**
     * Combines key with prefix
     *
     * @param string $key
     * @return string
     */
    protected function _makeKey($key)
    {
        return $this->_cachePrefix . $key;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $value = $this->offsetGet($offset);
        return $value !== null;
    }

    /**
     * Returns object, stored in cache, or null
     *
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        $value = $this->_memcache->get($this->_makeKey($offset));
        if ($value === false) {
            return null;
        } else {
            return $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->_memcache->set($this->_makeKey($offset), $value, 0, $this->_ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->_memcache->delete($this->_makeKey($offset));
    }
}