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
     * @var int|float|bool|string
     */
    private $_scalar = null;

    /**
     * @var array
     */
    private $_array = null;

    /**
     * Constructor
     *
     * @param mixed|null $data
     */
    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->set($data);
        }
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
     * Sets the value of chain node
     *
     * @param mixed $value
     */
    public function set($value)
    {
        if ($value instanceof ChainNode) {
            $this->_scalar = $value->_scalar;
            $this->_array = $value->_array;
        } else if (is_array($value) || $value instanceof \ArrayAccess) {
            // Using boxing
            foreach ($value as &$row) {
                if (!$row instanceof ChainNode) {
                    $row = new ChainNode($row);
                }
            }
            $this->_array = $value;
        } else {
            $this->_scalar = $value;
        }
    }

    /**
     * Returns flat representation
     *
     * @param string $prefix
     * @return mixed|null
     */
    public function flatten($prefix = '')
    {
        if ($this->_array === null) {
            return array();
        }

        $answer = array();
        foreach ($this as $key => $value) {
            if ($value instanceof ChainNode) {
                $answer = array_merge(
                    $answer,
                    $value->flatten($prefix . $key . '.')
                );
            } else {
                $answer[$prefix . $key] = $value;
            }

        }

        return $answer;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (empty($this->_array)) {
            return 0;
        }
        return count($this->_array);
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

        return (int) $this->_scalar;
    }

    /**
     * Returns float value
     * If not applicable, then returns $default, but if
     * default not set, throws exception
     *
     * @param mixed|null $default
     * @return float
     * @throws \LogicException
     */
    public function getFloat($default = null)
    {
        if (!$this->isFloat()) {
            if ($default !== null) {
                return $default;
            }
            throw new \LogicException('Node does not contain float value');
        }

        return (float) $this->_scalar;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if ($this->isArray()) {
            return new \ArrayIterator($this->_array);
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
        if ($this->_scalar === null) {
            if ($default !== null) {
                return $default;
            }
            throw new \LogicException('Node is empty and not contain string');
        }
        if (!$this->isString()) {
            throw new \LogicException('Node value is not a string');
        }

        return (string) $this->_scalar;
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
        if ($this->_scalar === null) {
            if ($default !== null) {
                return (string) $default;
            }
            throw new \LogicException('Node is empty and not contain string');
        }

        return (string) $this->_scalar;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->_array) && empty($this->_scalar);
    }

    /**
     * Returns true if internal data is array or
     * implements ArrayAccess interface
     *
     * @return bool
     */
    public function isArray()
    {
        return $this->_array !== null;
    }

    /**
     * Returns true if current data is boolean
     *
     * @return bool
     */
    public function isBool()
    {
        return $this->_scalar !== null && is_bool($this->_scalar);
    }

    /**
     * Returns true if internal data is int
     *
     * @return bool
     */
    public function isInt()
    {
        return $this->_scalar !== null && (
            is_int($this->_scalar)
            || $this->_scalar === (string) intval($this->_scalar)
        );
    }

    /**
     * Returns true if internal data is float
     *
     * @return bool
     */
    public function isFloat()
    {
        return $this->_scalar !== null && (
            is_float($this->_scalar)
            || $this->_scalar === (float) floatval($this->_scalar)
        );
    }

    /**
     * Returns true if internal data is null
     *
     * @return bool
     */
    public function isNull()
    {
        return $this->_array === null && $this->_scalar === null;
    }

    /**
     * Returns true if scalar value is null
     *
     * @return bool
     */
    public function isScalar()
    {
        return $this->_scalar !== null;
    }

    /**
     * Returns true if internal data is string
     *
     * @return bool
     */
    public function isString()
    {
        return $this->_scalar !== null && is_string($this->_scalar);
    }

    /**
     * Returns true if internal data is boolean and true
     *
     * @return bool
     */
    public function isTrue()
    {
        return $this->isBool() && $this->_scalar === true;
    }

    /**
     * Returns true if provided key exists in set
     * Alias for @see offsetExists
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$this->isArray()) {
            return false;
        }
        return isset($this->_array[$offset]);
    }

    /**
     * {@inheritdoc}
     * @return ChainNode
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            // Offset exists
            if (!$this->_array[$offset] instanceof ChainNode) {
                // Autoboxing
                $this->_array[$offset] = new ChainNode($this->_array[$offset]);
            }
            return $this->_array[$offset];
        }
        if (!$this->isArray()) {
            $this->_array = array();
        }
        return $this->_array[$offset] = new ChainNode(null);
    }

    /**
     * {@inheritdoc}
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        if ($this->isNull()) {
            $this->_array = array();
        } else if (!$this->isArray()) {
            throw new \BadMethodCallException(
                'Node is not array node for key ' . $offset
            );
        }

        $this->_array[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($this->isArray()) {
            unset($this->_array[$offset]);
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