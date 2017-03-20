<?php

use Composer\Autoload\ClassLoader;

function __db_connection_options()
{
    return [
        'driver'        => 'pdo_mysql',
        'dbname'        => "go1_dev",
        'host'          => 'mysql',
        'user'          => 'root',
        'password'      => 'root',
        'port'          => '3306',
        'driverOptions' => [1002 => 'SET NAMES utf8'],
    ];
}

if (isset($_SERVER['REQUEST_URI'])) {
    if (0 === strpos($_SERVER['REQUEST_URI'], '/GO1/')) {
        $_SERVER['REQUEST_URI'] = preg_replace('`^/GO1/[a-z0-9\\-]+-service/(.*)$`', '/$1', $_SERVER['REQUEST_URI']);
        $_SERVER['REQUEST_URI'] = preg_replace('`^/GO1/[a-z0-9\\-]+/(.*)$`', '/$1', $_SERVER['REQUEST_URI']);
    }

    if (0 === strpos($_SERVER['REQUEST_URI'], '/v3/')) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 3);
    }

    if (0 === strpos($_SERVER['REQUEST_URI'], '//')) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);
    }
}

/** @var ClassLoader $loader */
$loader = require_once "/app/vendor/autoload.php";
$loader->addPsr4('go1\\clients\\', '/app/libraries/util/clients');
$loader->addPsr4('go1\\util\\', '/app/libraries/util');

foreach ($loader->getPrefixesPsr4() as $ns => $paths) {
    if (0 === strpos($ns, 'go1\\')) {
        // All projects has 1 path for now.
        $path = $paths[0];
        $project = end(explode('/', $path));
        if (is_dir("/app/{$project}")) {
            $loader->setPsr4($ns, "/app/{$project}");
        }
        elseif (is_dir("/app/libraries/{$project}")) {
            $loader->setPsr4($ns, "/app/libraries/{$project}");
        }
    }
    elseif (in_array($ns, ['App\\', 'App\\Test\\', 'Embed\\Adapters\\', 'Embed\\Providers\\OEmbed\\'])) {
        // @todo Remove these hard codes.
        // All of these namespaces has 1 path for now.
        $path = $paths[0];
        $loader->setPsr4($ns, str_replace('/app/vendor/composer/../../php/', '/app/', $path));
    }
}

return $loader;
