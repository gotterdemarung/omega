<?php

namespace Omega\Config;

/**
 * Configuration implementation, that reads information from JSON file
 * Uses @see Omega\Type\ChainNode as backbone
 *
 * @package Omega\Config
 */
class JsonFileConfig extends ChainNodeConfig
{
    /**
     * Constructor
     *
     * @param string $filename
     * @throws \InvalidArgumentException If file not found
     * @throws \Exception                If file in wrong format
     */
    public function __construct($filename)
    {
        // Trying to read file
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new \InvalidArgumentException(
                "Configuration file {$filename} not found"
            );
        }

        $data = file_get_contents($filename);
        $json = @json_decode($data, true);
        if (!is_array($json)) {
            throw new \Exception(
                "File {$filename} is not valid JSON file"
            );
        }

        parent::__construct($json);
    }


}