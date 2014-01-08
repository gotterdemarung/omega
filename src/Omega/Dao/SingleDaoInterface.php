<?php

namespace Omega\Dao;
use Omega\Model\ModelInterface;

/**
 * Interface SingleDaoInterface
 * Interface for data access objects, used to retrieve/store information
 * in various ways
 *
 * @package Omega\Dao
 */
interface SingleDaoInterface
{
    /**
     * Finds and returns model with id
     *
     * @param string|ModelInterface $classNameOrModel
     * @param string|null           $collection
     * @param mixed|null            $id
     * @return null|ModelInterface
     * @throws \Exception
     */
    public function getById($classNameOrModel, $id = null, $collection = null);

    /**
     * Saves current model
     *
     * @param ModelInterface $model
     * @param string|null    $collection
     * @return void
     */
    public function save($model, $collection = null);

    /**
     * Deletes entry from collection
     *
     * @param mixed|ModelInterface $idOrModel
     * @param string|null          $collection
     * @return void
     */
    public function delete($idOrModel, $collection = null);
}