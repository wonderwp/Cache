<?php

use WonderWp\Component\Cache\DisabledCache;
use WonderWp\Component\Cache\FileCache;
use WonderWp\Component\Cache\TransientCache;
use WonderWp\Component\DependencyInjection\Container;

add_action('wonderwp.loader.load', 'wwp_register_cache_definitions_towards_container', 10, 2);

function wwp_register_cache_definitions_towards_container(Container $container)
{
    //Cache
    $container['wwp.cache.cache'] = function () {
        return new TransientCache();
    };

    //Direct access to the transient cache
    $container['wwp.cache.transient'] = function () {
        return new TransientCache();
    };

    //Direct access to the disabled cache
    $container['wwp.cache.disabled'] = function () {
        return new DisabledCache();
    };

    //Direct access to the file cache
    $container['wwp.cache.file'] = function () {
        $cacheDir = defined('WP_CONTENT_DIR') 
            ? WP_CONTENT_DIR . '/cache/wonderwp/' 
            : null;
        return new FileCache($cacheDir);
    };
}
