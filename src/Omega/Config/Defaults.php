<?php

namespace Omega\Config;

/**
 * Default setting for application
 *
 * @package Omega\Config
 */
class Defaults extends ChainNodeConfig
{
    /**
     * @return Defaults
     */
    public static function getInstance()
    {
        return new Defaults();
    }

    public function __construct()
    {
        parent::__construct(
            array(
                self::PATH_INTERNAL_ENCODING => 'UTF-8',
                self::PATH_CATCH_PHP_ERRORS  => true,
                self::PATH_ERROR_REPORTING   => E_ALL
            )
        );
    }
} 