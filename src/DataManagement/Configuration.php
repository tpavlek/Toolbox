<?php

namespace Depotwarehouse\Toolbox\DataManagement;

class Configuration {

    /** @var  array */
    public $pagination;

    /** @var  array */
    public $include;

    public function __construct() {
        $this->pagination = [
            'per_page' => 2,
            'page_name' => "page"
        ];
        $this->include = [
            'max_depth' => 5
        ];
    }

} 