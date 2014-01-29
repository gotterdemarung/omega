<?php

namespace Omega\Model;

/**
 * Class ArrayModel
 * Array based implementation of model
 *
 * @package Omega\Model
 * @todo tests
 */
class ArrayModel implements ModelInterface, \ArrayAccess
{
    /**
     * @var mixed
     */
    private $_id = null;
    /**
     * @var array
     */
    private $_data = array();
    /**
     * @var array
     */
    private $_changes = array();

    public function __construct($initialData = null)
    {
        if ($initialData !== null) {
            $this->setUp($initialData);
        }
    }

    /**
     * Setups model by provided data
     *
     * @param array $data
     * @return mixed
     */
    public function setUp(array $data)
    {
        $this->_changes = array();
        $this->_data = $data;
        $this->_id = $data['id'];
    }


    /**
     * Sets data of model
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    protected function _realSet($key, $value)
    {
        $this->_data[$key] = $value;
        $this->_changes[$key] = $value;
    }

    /**
     * Returns true if model has provided key
     *
     * @param string $key
     * @return bool
     */
    protected function _realExists($key)
    {
        return isset($this->_data[$key])
            || array_key_exists($key, $this->_data);
    }

    /**
     * Returns value of key
     *
     * @param string $key
     * @throws \Exception
     * @return mixed
     */
    protected function _realGet($key)
    {
        if (!$this->_realExists($key)) {
            throw new \Exception("Key $key not found");
        }
        return $this->_data[$key];
    }

    /**
     * Returns ID of current object
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns true if current model not saved in DB
     *
     * @return bool
     */
    public function isNewRecord()
    {
        return $this->getId() === null;
    }

    /**
     * Returns array of data to save
     *
     * @return array
     */
    public function getSaveData()
    {
        return $this->_changes;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->_realExists($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->_realGet($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->_realSet($offset, $value);
    }

    /**
     * Always throws exception
     *
     * @param string $offset
     * @throws \BadMethodCallException
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException(
            'Operation not permitted'
        );
    }


} 