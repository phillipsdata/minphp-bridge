<?php
namespace Minphp\Bridge;

use Minphp\Container\ContainerAwareInterface;
use Interop\Container\ContainerInterface;

/**
 * Initializer for the Bridge
 *
 * Obtains the container for use by bridged libraries to manage dependencies
 */
class Initializer implements ContainerAwareInterface
{
    private static $initializer;
    protected $container;

    /**
     * Singleton
     */
    private function __construct()
    {

    }

    /**
     * Fetch the instance of the Initializer
     *
     * @return Initializer
     */
    public static function get()
    {
        if (!self::$initializer) {
            self::$initializer = new self();
        }

        return self::$initializer;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }
}
