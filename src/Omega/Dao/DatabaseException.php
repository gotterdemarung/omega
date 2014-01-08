<?php

namespace Omega\Dao;


class DatabaseException extends \Exception
{
    private $_dao;
    private $_sql;

    /**
     * @param SingleDaoInterface $dao
     * @param string             $sql
     * @param string             $message
     * @param \Exception         $inner
     */
    function __construct(
        SingleDaoInterface $dao,
        $sql,
        $message = null,
        \Exception $inner = null
    )
    {
        parent::__construct(
            isset($message) ? $message : 'Database error',
            0,
            $inner
        );
        $this->_dao = $dao;
        $this->_sql = $sql;
    }

    /**
     * @return \Omega\Dao\SingleDaoInterface
     */
    public function getDao()
    {
        return $this->_dao;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->_sql;
    }


} 