# Minphp/Bridge

Bridges minPHP 0.x global classes to minPHP 1.x namespaced classes for backwards compatibility.

## Installation

Install via composer:

```sh
composer require minphp/bridge
```

## Usage

The bridge requires some information before it's able to initialize some
libraries. This is handled by populating and passing in a container that
implements `Minphp\Container\ContainerInterface`.

The following config files in minphp 0.x were removed in minphp 1.0, which is
what necessitates populating the container:

- core.php
- database.php
- routes.php
- session.php

minPHP uses the `Minphp\Container\Container`, which meets this requirement. The
following elements are required to be set:

- `minphp.cache` *array* containing:
    - `dir` *string*
    - `dir_permission` *int* (octal)
    - `extension` *string*
    - `enabled` *bool*
- `minphp.config` *array* containing:
    - `dir` *string*
- `minphp.constants` *array* containing:
    - `APPDIR` *string*
    - `CACHEDIR` *string*
    - `COMPONENTDIR` *string*
    - `CONFIGDIR` *string*
    - `CONTROLLERDIR` *string*
    - `DS` *string*
    - `HELPERDIR` *string*
    - `HTACCESS` *bool*
    - `LANGDIR` *string*
    - `LIBDIR` *string*
    - `MINPHP_VERSION` *string*
    - `MODELDIR` *string*
    - `PLUGINDIR` *string*
    - `ROOTWEBDIR` *string*
    - `VEDNORDIR` *string*
    - `VIEWDIR` *string*
    - `WEBDIR` *string*
- `minphp.language` *array* containing:
    - `default` *string* 'en_us'
    - `dir` *string*
    - `pass_through` *bool*
- `minphp.mvc` *array* containing the following keys:
    - `default_controller` *string*
    - `default_structure` *string*
    - `default_view` *string*
    - `view_extension` *string*
- `minphp.session` *array* containing the following keys (all optional):
    - `db` *array* containing:
        - `tbl` *string* The session database table
        - `tbl_id` *string* The ID database field
        - `tbl_exp` *string* The expiration database field
        - `tbl_val` *string* The value database field
    - `ttl` *int* Number of seconds to keep a session alive.
    - `cookie_ttl` *int* Number of seconds to keep long storage cookie alive.
    - `session_name` *string* Name of the session.
    - `session_httponly` *bool* True to enable HTTP only session cookies.
- `cache` *Minphp\Cache\Cache*
- `view` *View* As a factory (new instance each time)
- `loader` *Loader*
- `pdo` *PDO*

### Example Container

The following options fulfill the requirements of the bridge. These values also
happen to be the default values settings from minPHP 0.x.

```php
use Minphp\Container\Container;

$container = new Container();


$container->set('minphp.cache', function ($c) {
    return [
        'dir' => $c->get('minphp.constants')['CACHEDIR'],
        'dir_permissions' => 0755,
        'extension' => '.html',
        'enabled' => true
    ]
});

$container->set('minphp.config', function ($c) {
    return [
        'dir' => $c->get('minphp.constants')['CONFIGDIR']
    ];
});

$container->set('minphp.constants', function ($c) {
    $rootWebDir = realpath(dirname(dirname(dirname(__FILE__))))
        . DIRECTORY_SEPARATOR;

    $appDir = 'app' . DIRECTORY_SEPARATOR;
    $htaccess = file_exists($rootWebDir . '.htaccess');

    $script = isset($_SERVER['SCRIPT_NAME'])
        ? $_SERVER['SCRIPT_NAME']
        : (
            isset($_SERVER['PHP_SELF'])
            ? $_SERVER['PHP_SELF']
            : null
        );

    $webDir = (
        !$htaccess
        ? $script
        : (
            ($path = dirname($script)) === '/'
            || $path == DIRECTORY_SEPARATOR ? '' : $path
        )
    ) . '/';

    if ($webDir === $rootWebDir) {
        $webDir = '/';
    }


    return [
        'APPDIR' => $appDir,
        'CACHEDIR' => $rootWebDir . 'cache' . DIRECTORY_SEPARATOR,
        'COMPONENTDIR' => $rootWebDir . 'components' . DIRECTORY_SEPARATOR,
        'CONFIGDIR' => $rootWebDir . 'config' . DIRECTORY_SEPARATOR,
        'CONTROLLERDIR' => $rootWebDir . $appDir . 'controllers' . DIRECTORY_SEPARATOR,
        'DS' => DIRECTORY_SEPARATOR,
        'HELPERDIR' => $rootWebDir . 'helpers' . DIRECTORY_SEPARATOR,
        'HTACCESS' => $htaccess,
        'LANGDIR' => $rootWebDir . 'language' . DIRECTORY_SEPARATOR,
        'LIBDIR' => $rootWebDir . 'lib' . DIRECTORY_SEPARATOR,
        'MINPHP_VERSION' => '1.0.0',
        'MODELDIR' => $rootWebDir . $appDir . 'models' . DIRECTORY_SEPARATOR,
        'PLUGINDIR' => $rootWebDir . 'plugins' . DIRECTORY_SEPARATOR,
        'ROOTWEBDIR' => $rootWebDir,
        'VEDNORDIR' => $rootWebDir . 'vendors' . DIRECTORY_SEPARATOR,
        'VIEWDIR' => $rootWebDir . $appDir . 'views' . DIRECTORY_SEPARATOR,
        'WEBDIR' => $webDir
    ];
});

$container->set('minphp.language', function ($c) {
    return [
        'default' => 'en_us',
        'dir' => $c->get('minphp.constants')['LANGDIR'],
        'pass_through' => false
    ];
});

$container->set('minphp.mvc', function ($c) {
    return [
        'default_controller' => 'main',
        'default_structure' => 'structure',
        'default_view' => 'default',
        'view_extension' => '.pdt'
    ];
});

$container->set('minphp.session', function ($c) {
    return [
        'db' => [
            'tbl' => 'sessions',
            'tbl_id' => 'id',
            'tbl_exp' => 'expire',
            'tbl_val' => 'value'
        ],
        'ttl' => 1800, // 30 mins
        'cookie_ttl' => 604800, // 7 days
        'session_name' => 'sid',
        'session_httponly' => true
    ];
});

$container->set('cache', function ($c) {
    return Cache::get();
});

$container->set('view', $container->factory(function ($c) {
    return new View();
}));

$container->set('loader', function ($c) {
    $constants = $c->get('minphp.constants');
    $loader = Loader::get();
    $loader->setPaths([
        $constants['APPDIR'],
        'models' => $constants['MODELDIR'],
        'controllers' => $constants['CONTROLLERDIR'],
        'components' => $constants['COMPONENTDIR'],
        'helpers' => $constants['HELPERDIR'],
        'plugins' => $constants['PLUGINDIR']
    ]);

    return $loader;
});

$container->set('pdo', function ($c) {
    return new PDO('...', '...', '...');
});



\Minphp\Bridge\Initializer::get()
    ->setContainer($container)
    ->run();
```
