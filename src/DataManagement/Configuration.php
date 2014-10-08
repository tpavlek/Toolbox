<?php

namespace Depotwarehouse\Toolbox\DataManagement;

class Configuration {

    /** @var  array */
    public $pagination;

    /** @var  array */
    public $include;

    public function __construct() {
        $this->setPagination();
        $this->setInclude();
    }

    /**
     * Sets the pagination configuration object.
     * @param int $per_page
     * @param string $page_name Name of the $_GET parameter that specifies current page number
     */
    public function setPagination($per_page = 2, $page_name = "page") {
        $this->pagination = [
            "per_page" => $per_page,
            "page_name" => $page_name
        ];
    }

    /**
     * Sets the include configuration object.
     * @param int $max_depth The number of recursions we are willing to do.
     */
    public function setInclude($max_depth = 5) {
        $this->include = [
            "max_depth" => $max_depth
        ];
    }

} 