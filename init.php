<?php

use WonderWp\Component\Cache\TransientCache;
use WonderWp\Component\DependencyInjection\Container;

add_action('wonderwp.loader.load', 'wwp_register_cache_definitions_towards_container', 10, 2);

function wwp_register_cache_definitions_towards_container(Container $container)
{
    //Cache
    $container['wwp.cache.cache'] = function () {
        return new TransientCache();
    };
}
