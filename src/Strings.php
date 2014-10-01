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

    /**
     * Parses single or comma-separated lists of IDs into arrays
     * @param string $ids Either a single ID or a comma-separated list of IDs
     * @return array|string An array of IDs or a single ID
     */
    static function parseCommaSeparatedIDs($ids) {
        $list = array_unique(explode(',', $ids));
        if (count($list) > 1) {
            sort($list);
            return $list;
        }

        return $ids;
    }
}