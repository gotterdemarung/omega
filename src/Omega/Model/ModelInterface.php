<?php

namespace Omega\Model;

interface ModelInterface extends IdentifiedInterface
{
    /**
     * Setups model by provided data
     *
     * @param array $data
     * @return mixed
     */
    public function setUp(array $data);

    /**
     * Returns true if current model not saved in DB
     *
     * @return bool
     */
    public function isNewRecord();

    /**
     * Returns array of data to save
     *
     * @return array
     */
    public function getSaveData();
}