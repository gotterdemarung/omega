<?php

namespace Omega\Db;

interface HashMapperInterface
{
    /**
     * Converts object to hashmap (associative array)
     *
     * @param object $object
     * @return array
     */
    public function toHashMap($object);

    /**
     * Converts hashmap into object
     *
     * @param array $hashmap
     * @return object
     */
    public function fromHashMap(array $hashmap);
}