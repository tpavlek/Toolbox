<?php

namespace Depotwarehouse\Toolbox\Operations;

use Depotwarehouse\Toolbox\Exceptions\ArrayEmptyException;

class Operation {

    const INCLUDE_PATH_KEY = ":";

    /** @var  string */
    public $key;
    /** @var  array */
    public $include_path;
    /** @var  string */
    public $operation;
    /** @var  string */
    public $value;

    public function __construct($path, $operation, $value) {
        $this->computeIncludePath($path);
        $this->operation = $operation;
        $this->value = $value;
    }

    /**
     * Checks if there are any keys in the include path
     * @return bool
     */
    public function hasIncludes() {
        return count($this->include_path) > 0;
    }

    /**
     * Removes the first element from the include_path and returns it to the user
     * @throws ArrayEmptyException
     * @return string
     */
    public function pullInclude() {
        if (!$this->hasIncludes()) {
            throw new ArrayEmptyException("Could not get next include - include path is empty");
        }

        return array_shift($this->include_path);
    }

    private function computeIncludePath($path) {
        if (strpos($path, self::INCLUDE_PATH_KEY) !== FALSE) {
            $array = explode(self::INCLUDE_PATH_KEY, $path);
            $this->key = array_pop($array);
            $this->include_path = $array;
            return;
        }

        $this->include_path = array();
        $this->key = $path;
    }

}