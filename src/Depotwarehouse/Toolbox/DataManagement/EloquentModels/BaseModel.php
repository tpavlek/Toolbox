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

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->fillable = array();
        $this->guarded = array();
        if (!is_null($this->meta)) {
            foreach ($this->meta as $property => $flags) {
                /** @var $property string */
                foreach ($flags as $flag) {
                    $this->{$flag}[]= $property;
                }
            }
        }
    }

} 