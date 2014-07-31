<?php
/**
 * Created by PhpStorm.
 * User: ebon
 * Date: 7/29/14
 * Time: 9:15 AM
 */

namespace Depotwarehouse\Toolbox\DataManagement\EloquentModels;


class BaseModel extends \Eloquent {

    const UPDATEABLE = 'updateable';
    const FILLABLE = 'fillable';
    const GUARDED = 'guarded';
    const SEARCHABLE = 'searchable';

    public $updateable = array();
    public $searchable = array();

    public function __construct() {
        parent::__construct();
        $this->fillable = array();
        $this->guarded = array();

        foreach ($this->meta as $property => $flags) {
            /** @var $property string */
            foreach ($flags as $flag) {
                $this->{$flag}[]= $property;
            }
        }
    }

} 