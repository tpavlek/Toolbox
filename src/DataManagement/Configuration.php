<?php

namespace Depotwarehouse\Toolbox\DataManagement;

class Configuration {

    /** @var  array */
    public $pagination;

    public function __construct() {
        $this->pagination = [
            'per_page' => 5
        ];
    }

} 