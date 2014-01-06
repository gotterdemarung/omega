<?php

namespace Omega\Config;


class Defaults extends Hardcoded
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
        $this->set(self::PATH_INTERNAL_ENCODING, 'UTF-8');
        $this->set(self::PATH_CATCH_PHP_ERRORS, true);
    }
} 