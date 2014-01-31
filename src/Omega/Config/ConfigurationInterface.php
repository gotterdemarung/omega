<?php

namespace Omega\Config;


interface ConfigurationInterface
{

    const PATH_INTERNAL_ENCODING    = 'application.php.encoding';
    const PATH_TIMEZONE             = 'application.php.timezone';
    const PATH_DISPLAY_ERRORS       = 'application.php.displayErrors';
    const PATH_ERROR_REPORTING      = 'application.php.errorReporting';
    const PATH_CATCH_PHP_ERRORS     = 'application.php.catchPhpErrors';
    const PATH_APP_IMPLEMENTATIONS  = 'application.services';

    /**
     * Returns true if current configuration has provided path
     *
     * @param string $path
     * @return bool
     */
    public function has($path);

    /**
     * Returns boolean
     *
     * @param string $path
     * @return bool
     * @throws ConfigurationException
     */
    public function getBool($path);

    /**
     * Returns string
     *
     * @param string $path
     * @return string
     */
    public function getString($path);

    /**
     * Returns integer
     *
     * @param string $path
     * @return int
     * @throws ConfigurationException
     */
    public function getInteger($path);

    /**
     * Returns float
     *
     * @param string $path
     * @return float
     * @throws ConfigurationException
     */
    public function getFloat($path);

    /**
     * Returns array
     *
     * @param $path
     * @return array
     */
    public function getArray($path);

    /**
     * Returns boolean, and if not set - $default
     *
     * @param string $path
     * @param bool   $default
     * @return bool
     */
    public function getBoolSafe($path, $default);

    /**
     * Returns string, and if not set - $default
     *
     * @param string $path
     * @param string $default
     * @return string
     */
    public function getStringSafe($path, $default);

    /**
     * Returns integer, and if not set - $default
     *
     * @param string $path
     * @param int    $default
     * @return int
     */
    public function getIntegerSafe($path, $default);

    /**
     * Returns float, and if not set - $default
     *
     * @param string $path
     * @param float  $default
     * @return float
     */
    public function getFloatSafe($path, $default);

    /**
     * Returns flat representations of config
     *
     * @return array
     */
    public function getFlatList();
}