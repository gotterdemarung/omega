<?php

namespace Omega\Type;

/***
 * Class Node
 * Magic class for entities, which could be either scalar or
 * array values.
 * Returns new Node instead of null on accessing in array access
 * way on non-existing keys
 *
 * Common usage - configuration files
 *
 * @package Omega\Type
 */
class ChainNode implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var mixed
     */
    private $_data;

    /**
     * Constructor
     *
     * @param mixed|null $data
     */
    public function __construct($data = null)
    {
        $this->_data = $data;
    }

    /**
     * Magic alias for offsetGet
     *
     * @param string $name
     * @return ChainNode
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Magic alias for offsetSet
     *
     * @param string $name
     * @param mixed  $value
     */
    function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (empty($this->_data)) {
            return 0;
        }
        if (is_array($this->_data) || ($this->_data instanceof \Countable)) {
            return count($this->_data);
        }
        return 0;
    }

    /**
     * Returns integer value
     * If not applicable, then returns $default, but if
     * default not set, throws exception
     *
     * @param mixed|null $default
     * @return int
     * @throws \LogicException
     */
    public function getInt($default = null)
    {
        if (!$this->isInt()) {
            if ($default !== null) {
                return $default;
            }
            throw new \LogicException('Node does not contain int value');
        }

        return (int) $this->_data;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        // We need to wrap everything before iterating
        if ($this->isTraversable()) {
            foreach ($this->_data as $key => $value) {
                if ($value instanceof ChainNode) {
                    continue;
                } else {
                    $this->_data[$key] = new ChainNode(
                        $value
                    );
                }
            }
            return new \ArrayIterator($this->_data);
        } else {
            return new \EmptyIterator();
        }
    }


    /**
     * Returns string value
     * If not applicable, then returns $default, but if
     * default not set, throws exception
     *
     * Does not cast to string!
     *
     * @param mixed|null $default
     * @return string
     * @throws \LogicException
     */
    public function getString($default = null)
    {
        if ($this->isEmpty()) {
            if ($default !== null) {
                return $default;
            }
            throw new \LogicException('Node is empty and not contain string');
        }
        if (!$this->isString()) {
            throw new \LogicException('Node value is not a string');
        }

        return (string) $this->_data;
    }

    /**
     * Returns string value and enforces string casting
     * if internal value is not a string
     *
     * @param null $default
     * @return string
     * @throws \LogicException
     */
    public function castString($default = null)
    {
        if ($this->isEmpty()) {
            if ($default !== null) {
                return (string) $default;
            }
            throw new \LogicException('Node is empty and not contain string');
        }

        return (string) $this->_data;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->_data);
    }

    /**
     * Returns true if internal data is array or
     * implements ArrayAccess interface
     *
     * @return bool
     */
    public function isArrayAccess()
    {
        return is_array($this->_data) || ($this->_data instanceof \ArrayAccess);
    }

    /**
     * Returns true if current data is boolean
     *
     * @return bool
     */
    public function isBool()
    {
        return !$this->isNull() && is_bool($this->_data);
    }

    /**
     * Returns true if internal data is int
     *
     * @return bool
     */
    public function isInt()
    {
        if ($this->isNull() || is_object($this->_data)) {
            return false;
        }
        return is_int($this->_data)
        || $this->_data === (string) intval($this->_data);
    }

    /**
     * Returns true if internal data is null
     *
     * @return bool
     */
    public function isNull()
    {
        return $this->_data === null;
    }

    /**
     * Returns true if internal data is string
     *
     * @return bool
     */
    public function isString()
    {
        if ($this->isNull() || is_object($this->_data)) {
            return false;
        }
        return is_string($this->_data);
    }

    /**
     * Returns true if internal data is traversable
     *
     * @return bool
     */
    public function isTraversable()
    {
        if ($this->isNull()) {
            return false;
        }

        return is_array($this->_data) || $this->_data instanceof \Traversable;
    }

    /**
     * Returns true if internal data is boolean and true
     *
     * @return bool
     */
    public function isTrue()
    {
        return $this->isBool() && $this->_data === true;
    }

    /**
     * Returns true if current object equals to provided
     *
     * @param mixed $object
     * @return bool
     */
    public function equals($object)
    {
        if ($object instanceof ChainNode) {
            return $this->_data === $object->_data;
        }

        if ($object === null) {
            return $this->isNull();
        }
        if (is_bool($object) && $this->isBool()) {
            return $this->_data === $object;
        }
        if (is_int($object) && $this->isInt()) {
            return $this->_data === $object;
        }
        if (is_float($object) && is_float($this->_data)) {
            return $this->_data === $object;
        }
        if (is_string($object) && $this->isString()) {
            return $this->_data === $object;
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$this->isArrayAccess()) {
            return false;
        }
        return isset($this->_data[$offset]);
    }

    /**
     * {@inheritdoc}
     * @return ChainNode
     */
    public function offsetGet($offset)
    {
        if (!$this->isArrayAccess() || !$this->offsetExists($offset)) {
            return new ChainNode(null);
        }
        if ($this->_data[$offset] instanceof ChainNode) {
            return $this->_data[$offset];
        } else {
            // Wrapping
            return $this->_data[$offset] = new ChainNode(
                $this->_data[$offset]
            );
        }
    }

    /**
     * {@inheritdoc}
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        if ($this->isNull()) {
            $this->_data = array();
        } else if (! $this->isArrayAccess()) {
            throw new \BadMethodCallException(
                'Node is not array node'
            );
        }

        $this->_data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->isArrayAccess()) {
            unset($this->_data[$offset]);
        }
    }

    /**
     * Returns ChainNode in requested path
     * Path delimiter is . (dot)
     * Example:
     * settings.database.username
     *
     * @param string $path
     *
     * @return ChainNode
     */
    public function path($path)
    {
        if (strpos($path, '.') === false) {
            return $this->offsetGet($path);
        }
        $path = explode('.', $path);
        return $this->offsetGet($path[0])->path(
            implode('.', array_slice($path, 1))
        );
    }

} 