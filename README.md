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

```php
// Assuming $container is already defined with the required elements, for example:

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

// ...


\Minphp\Bridge\Initializer::get()
    ->setContainer($container)
    ->run();
```
