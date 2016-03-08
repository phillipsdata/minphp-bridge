<?php

use Minphp\Bridge\Initializer;
use Minphp\Session\Session as MinphpSession;
use Minphp\Session\Handlers\PdoHandler;

/**
 * Session Bridge
 *
 * Intended for legacy backwards compatibility ONLY.
 * Use Minphp\Session\Session instead.
 */
class Session
{
    private static $session = null;
    private static $instances = 0;
    private $config = [];

    /**
     * Initialize
     */
    public function __construct()
    {
        $container = Initializer::get()->getContainer();
        $this->config = $container->get('minphp.session');

        self::$instances++;
        if (self::$session instanceof MinphpSession) {
            return;
        }

        $options = [];

        if (array_key_exists('ttl', $this->config)) {
            $options['cookie_lifetime'] = $this->config['ttl'];
        }

        if (array_key_exists('session_name', $this->config)) {
            $options['name'] = $this->config['session_name'];
        }

        if (array_key_exists('session_httponly', $this->config)) {
            $options['cookie_httponly'] = $this->config['session_httponly'];
        }

        $this->session = new MinphpSession(
            new PdoHandler(
                $container->get('pdo'),
                array_key_exists('db', $this->config)
                ? $this->config['db']
                : []
            ),
            $options
        );

        $this->session->start();
    }

    /**
     * Close the session
     */
    public function __destruct()
    {
        --self::$instances;
        if (self::$instances <= 0) {
            self::$session->save();
        }
    }

    /**
     * Return the session ID
     *
     * @return string
     */
    public function getSid()
    {
        return self::$session->getId();
    }

    /**
     * Read session data
     *
     * @param string $name The key to read
     * @return mixed
     */
    public function read($name)
    {
        return self::$session->read($name);
    }

    /**
     * Writes a value to the session
     *
     * @param string $name The key to write
     * @param mixed $value The value to write
     */
    public function write($name, $value)
    {
        self::$session->write($name, $value);
    }

    /**
     * Unsets a valur, or all values from the session
     *
     * @param string $name The key to unset
     */
    public function clear($name = null)
    {
        self::$session->clear($name);
    }

    /**
     * The session cookie creation is handled automatically by PHP so this method
     * is left merely for backwards compatibility.
     *
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function setSessionCookie($path = '', $domain = '', $secure = false, $httponly = false)
    {
        // handled automatically
    }

    /**
     * Set long term storage of session cookie
     *
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function keepAliveSessionCookie($path = '', $domain = '', $secure = false, $httponly = false)
    {
        $options = [
            'cookie_path' => $path,
            'cookie_domain' => $domain,
            'cookie_secure' => $secure,
            'cookie_httponly' => $httponly
        ];
        $lifetime = array_key_exists('cookie_ttl', $this->config)
            ? $this->config['cookie_ttl']
            : null;

        self::$session->setOptions($options);
        self::$session->regenerate(true, $lifetime);
    }

    /**
     * Remove long term storage of session cookie
     *
     * @param string $path
     * @param string $domain
     * @param bool $secure
     */
    public function clearSessionCookie($path = '', $domain = '', $secure = false)
    {
        $options = [
            'cookie_path' => $path,
            'cookie_domain' => $domain,
            'cookie_secure' => $secure
        ];
        $lifetime = array_key_exists('ttl', $this->config)
            ? $this->config['ttl']
            : 0;

        self::$session->setOptions($options);
        self::$session->regenerate(true, $lifetime);
    }
}