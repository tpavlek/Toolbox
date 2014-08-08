<?php

namespace Depotwarehouse\Toolbox;

class Strings {
    /**
     * Generates a random alphanumeric string, with default length of 40 characters
     * @param int $length Length of the string, default 40
     * @return string A random string
     */
    static function generateRandomString($length = 40) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}