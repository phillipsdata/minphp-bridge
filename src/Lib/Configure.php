<?php

use Minphp\Bridge\Initializer;
use Minphp\Configure\Configure as MinphpConfigure;

/**
 * Configure Bridge
 *
 * Intended for legacy backwards compatibility ONLY.
 * Use Minphp\Configure\Configure instead.
 */
class Configure
{
    private static $configure;
    private static $config;

    /**
     * Singleton
     */
    private function __construct()
    {
        $container = Initializer::get()->getContainer();
        self::$config = $container->get('minphp.config');
        self::$configure = new MinphpConfigure();
    }

    /**
     * Fetches the Configure instance
     *
     * @return Minphp\Configure\Configure
     */
    private static function getInstance()
    {
        if (!self::$configure) {
            new self();
        }
        return self::$configure;
    }

    /**
     * Fetches a setting from the config
     *
     * @param string $name
     */
    public static function get($name)
    {
        return self::getInstance()->get($name);
    }

    /**
     * Checks if the setting exists
     *
     * @param string $name
     */
    public static function exists($name)
    {
        return self::getInstance()->exists($name);
    }

    /**
     * Removes the setting
     *
     * @param string $name
     */
    public static function free($name)
    {
        self::getInstance()->remove($name);
    }

    /**
     * Set a setting
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value)
    {
        self::getInstance()->set($name, $value);
    }

    /**
     * Load a config file of the format 'myconfig' where 'minconfig.php' exists
     *
     * @param string $file The name of the file excluding the '.php' extension
     * @param string $dir The directory the file exists in
     */
    public static function load($file, $dir = null)
    {
        $file .= '.php';

        if (null === $dir) {
            $dir = array_key_exists('dir', self::$config)
                ? self::$config['dir']
                : null;
        }

        foreach (self::readFile($dir . $file) as $name => $value) {
            self::getInstance()->set($name, $value);
        }
    }

    /**
     * Reads the given config file. Supports both config files that define
     * `$config` variable options, as well as return an array.
     *
     * @param string $file
     * @return array
     */
    private static function readFile($file)
    {
        $options = [];
        if (file_exists($file)) {
            $options = include_once $file;

            if (isset($config) && is_array($config)) {
                $options = $config;
                unset($config);
            }

            if (!is_array($options)) {
                $options = [];
            }
        }

        return $options;
    }

    /**
     * Sets error reporting level
     *
     * @param int $level
     */
    public static function errorReporting($level)
    {
        error_reporting($level);
    }
}
