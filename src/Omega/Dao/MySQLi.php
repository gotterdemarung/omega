<?php

namespace Omega\Dao;
use Omega\Model\HasCollectionInterface;
use Omega\Model\IdentifiedInterface;
use Omega\Model\ModelInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Class MySQLi
 *
 * @package Omega\Dao
 * @todo tests
 */
class MySQLi implements SingleDaoInterface
{
    /**
     * @var \MySQLi
     */
    private $_connection;

    /**
     * Creates new instance
     *
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string string $host
     * @param int $port
     */
    public function __construct(
        $user,
        $password,
        $db,
        $host = 'localhost',
        $port = 3306
    )
    {
        $this->_connection = new \MySQLi($host, $user, $password, $db, $port);
    }

    /**
     * Returns MySQLi connection
     *
     * @return \MySQLi
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Returns prepared statement
     *
     * @param string     $sql
     * @param array|null $binds
     * @return \mysqli_stmt
     * @throws DatabaseException
     */
    public function prepareStatement($sql, array $binds = null)
    {
        $stmt = $this->getConnection()->prepare($sql);
        if ($stmt === false) {
            throw new DatabaseException(
                $this,
                $sql,
                'Unable to create statement'
            );
        }
        if ($binds !== null) {
            foreach ($binds as $var) {
                if (is_int($var)) {
                    $stmt->bind_param('i', $var);
                } else if (is_float($var)) {
                    $stmt->bind_param('d', $var);
                } else {
                    $stmt->bind_param('s', $var);
                }
            }
        }
        return $stmt;
    }

    /**
     * Executes query and returns 2d array of results
     *
     * @param string|\mysqli_stmt $sqlOrStatement
     * @param array|null          $binds
     * @return array
     * @throws DatabaseException
     */
    public function executeRead($sqlOrStatement, $binds = null)
    {
        if (is_string($sqlOrStatement)) {
            return $this->executeRead(
                $this->prepareStatement($sqlOrStatement, $binds)
            );
        }
        if (!$sqlOrStatement instanceof \mysqli_stmt) {
            throw new DatabaseException($this, null, 'Expecting mysqli_stmt');
        }

        /** @var \mysqli_stmt $sqlOrStatement */
        $flag = $sqlOrStatement->execute();
        if ($flag !== true) {
            throw new DatabaseException($this, null, 'Query execution failed');
        }

        if ($sqlOrStatement->num_rows == 0) {
            return array();
        }

        $answer = array();
        $result = $sqlOrStatement->get_result();
        while ($row = $result->fetch_assoc()) {
            $answer[] = $row;
        }

        return $answer;
    }

    /**
     * Executes query
     *
     * @param string|\mysqli_stmt $sqlOrStatement
     * @param array|null          $binds
     * @return void
     * @throws DatabaseException
     */
    public function executeWrite($sqlOrStatement, $binds = null)
    {
        if (is_string($sqlOrStatement)) {
            $this->executeWrite(
                $this->prepareStatement($sqlOrStatement, $binds)
            );
            return;
        }
        if (!$sqlOrStatement instanceof \mysqli_stmt) {
            throw new DatabaseException($this, null, 'Expecting mysqli_stmt');
        }
        /** @var \mysqli_stmt $sqlOrStatement */
        $flag = $sqlOrStatement->execute();
        if ($flag !== true) {
            throw new DatabaseException($this, null, 'Query execution failed');
        }
    }

    /**
     * Finds and returns model with id
     *
     * @param string|ModelInterface|HasCollectionInterface $classNameOrModel
     * @param string|null                                  $collection
     * @param mixed|null                                   $id
     * @return null|ModelInterface
     * @throws \Exception
     */
    public function getById($classNameOrModel, $id = null, $collection = null)
    {
        // Overriding
        if ($collection === null) {
            if ($classNameOrModel instanceof HasCollectionInterface) {
                $collection = $classNameOrModel->getCollection();
            } else {
                throw new \InvalidArgumentException(
                    'Expecting HasCollectionInterface when collection not set'
                );
            }
        }
        if ($id === null) {
            if ($classNameOrModel instanceof IdentifiedInterface) {
                $id = $classNameOrModel->getId();
            }
        }
        if ($id === null) {
            throw new \InvalidArgumentException(
                'Expecting not-new IdentifiedInterface when id not set'
            );
        }

        // Creating statement
        $array = $this->executeRead(
            "SELECT * FROM `{$collection}` WHERE `{$id}` = ? LIMIT 1",
            array($id)
        );

        if (count($array) != 1) {
            return null;
        }

        // Inflating data
        if ($classNameOrModel instanceof ModelInterface) {
            $classNameOrModel->setUp($array[0]);
            return $classNameOrModel;
        }

        return new $classNameOrModel($array[0]);
    }

    /**
     * Saves current model
     *
     * @param ModelInterface $model
     * @param string|null $collection
     * @throws \InvalidArgumentException
     * @return void
     */
    public function save($model, $collection = null)
    {
        // Overriding
        if ($collection === null) {
            if ($model instanceof HasCollectionInterface) {
                $collection = $model->getCollection();
            } else {
                throw new \InvalidArgumentException(
                    'Expecting HasCollectionInterface when collection not set'
                );
            }
        }
        $id = $model->getId();
        if ($id === null) {
            throw new \InvalidArgumentException(
                'Expecting not-new IdentifiedInterface when id not set'
            );
        }

        // Variables
        $changes = $model->getSaveData();
        $binds = array();
        $sqlParts = array();
        if (count($changes) === 0) {
            // Nothing to save
            return;
        }
        foreach ($changes as $key => $value) {
            $sqlParts[] = "`{$key}` = ?";
            $binds[] = $value;
        }

        $sqlParts = implode(', ', $sqlParts);
        $binds[] = $id;
        $this->executeWrite(
            "UPDATE `{$collection}` SET {$sqlParts} WHERE `id` = ? LIMIT 1",
            $binds
        );
    }

    /**
     * Deletes entry from collection
     *
     * @param mixed|ModelInterface $idOrModel
     * @param string|null $collection
     * @throws \InvalidArgumentException
     * @return void
     */
    public function delete($idOrModel, $collection = null)
    {
        // Overriding
        if ($collection === null) {
            if ($idOrModel instanceof HasCollectionInterface) {
                $collection = $idOrModel->getCollection();
            } else {
                throw new \InvalidArgumentException(
                    'Expecting HasCollectionInterface when collection not set'
                );
            }
        }
        if ($idOrModel === null) {
            throw new \InvalidArgumentException(
                'Expecting not-new IdentifiedInterface when id not set'
            );
        } else if ($idOrModel instanceof IdentifiedInterface) {
            $idOrModel = $idOrModel->getId();
        }

        $this->executeWrite(
            "DELETE FROM `{$collection}` WHERE `id` = ? LIMIT 1",
            array($idOrModel)
        );
    }


} 