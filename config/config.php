<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api;

use ArrayObject;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

/**
 * Configuration files are loaded in a specific order. First ``global.php``, then ``*.global.php``.
 * then ``local.php`` and finally ``*.local.php``. This way local settings overwrite global settings.
 *
 * The configuration can be cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 *
 * Obviously, if you use closures in your config you can't cache it.
 */

$cachedConfigFile = 'data/cache/app_config.php';

if (is_file($cachedConfigFile)) {
    return new ArrayObject($cachedConfigFile, ArrayObject::ARRAY_AS_PROPS);
}

$config = [];

// Load configuration from autoload path
foreach (Glob::glob('config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

$mongoClientFactoryConfigFile = 'config/autoload/mongo_client.local.php';

if (file_exists($mongoClientFactoryConfigFile)) {
    $mongoClientFactoryConfig = include $mongoClientFactoryConfigFile;
    $config['dependencies']['factories']['mongo_client'] = $mongoClientFactoryConfig['mongo_client'];
}

// Cache config if enabled
if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
    file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ';');
}

// Return an ArrayObject so we can inject the config as a service in Aura.Di
// and still use array checks like ``is_array``.
return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
