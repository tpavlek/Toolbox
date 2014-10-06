<?php

namespace Depotwarehouse\Toolbox\DataManagement;

class Configuration {

    /** @var  array */
    public $pagination;

    /** @var  array */
    public $include;

    public function __construct() {
        $this->pagination = [
            'per_page' => 2
        ];
        $this->include = [
            'max_depth' => 5
        ];
    }

} 