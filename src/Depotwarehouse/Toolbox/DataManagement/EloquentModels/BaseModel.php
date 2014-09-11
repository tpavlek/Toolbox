<?php

namespace Depotwarehouse\Toolbox\DataManagement\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    const UPDATEABLE = 'updateable';
    const FILLABLE = 'fillable';
    const GUARDED = 'guarded';
    const SEARCHABLE = 'searchable';

    public $fillable = array();
    public $updateable = array();
    public $searchable = array();
    public $guarded = array('*');
    public $relatedModels = array();

    protected $meta = array();

    public function __construct(array $attributes = array()) {
        if (!is_null($this->meta) && count($this->meta) > 0) {
            $this->processMeta($this->meta);
        }
        parent::__construct($attributes);
    }

    public function setMeta(array $meta = array()) {
        $this->meta = $meta;
        $this->processMeta($meta);
    }

    private function processMeta(array $meta) {
        $this->guarded = array();
        foreach ($meta as $property => $flags) {
            /** @var $property string */
            foreach ($flags as $flag) {
                $this->{$flag}[]= $property;
            }
        }
    }

} 