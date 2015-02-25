<?php

namespace Depotwarehouse\Toolbox\Strings;

/**
 * Generates a random alphanumeric string, with default length of 40 characters.
 *
 * Note that this string is pseudorandom, effective for cases where you want garbage data or keys for a database.
 * It should not be used where security is a concern or for cryptography.
 *
 * @param  int $length Length of the string, default 40
 * @return string
 */
function generate_random_string($length = 40)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function starts_with($haystack, $needle)
{
    return ($needle != '' && strpos($haystack, $needle) === 0);
}

function ends_with($haystack, $needle)
{
    return ((string) $needle === substr($haystack, -strlen($needle)));
}
