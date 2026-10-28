<?php

declare(strict_types=1);

namespace {
    if (!function_exists('get_transient')) {
        function get_transient($transient)
        {
            return $GLOBALS['wwp_test_transients'][$transient] ?? false;
        }
    }

    if (!function_exists('set_transient')) {
        function set_transient($transient, $value, $expiration = 0)
        {
            $GLOBALS['wwp_test_transients'][$transient] = $value;

            return true;
        }
    }

    if (!function_exists('delete_transient')) {
        function delete_transient($transient)
        {
            unset($GLOBALS['wwp_test_transients'][$transient]);

            return true;
        }
    }
}
