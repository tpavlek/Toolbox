<?php

namespace Depotwarehouse\Toolbox\DataManagement\EloquentModels;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * Used as a helpful wrapper around Eloquent Models to extend common functionality and reduce boilerplate.
 * @package Depotwarehouse\Toolbox\DataManagement\EloquentModels
 */
class BaseModel extends Model {

    /**
     * Denotes the names of the internal arrays listed below.
     */
    const UPDATEABLE = 'updateable';
    const FILLABLE = 'fillable';
    const GUARDED = 'guarded';
    const SEARCHABLE = 'searchable';

    /**
     * Denotes the attributes within as white-listed for mass-assignment.
     * @var array
     */
    public $fillable = array();
    /**
     * Denotes the attributes within the array as updateable (not set permanently)
     * @var array
     */
    public $updateable = array();
    /**
     * Denotes all attributes within the array as something that could reasonably have a search performed using it as a key.
     * @var array
     */
    public $searchable = array();
    /**
     * Guarded attributes prevent against mass assignment on any of the listed attributes.
     * The default of '*', protects every attribute of the model from mass assignment.
     * @var array
     */
    public $guarded = array('*');
    /**
     * @var array
     */
    public $relatedModels = array();

    /**
     * Array containing meta information about the Model.
     *
     * Format is as such:
     * ```.language-php
     * [
     *     'column_name' => [ BaseModel::FILLABLE ],
     *     'other_column' => [ BaseModel::FILLABLE, BaseModel::UPDATEABLE ]
     * ]
     * ```
     * @var array
     */
    protected $meta = array();

    public function __construct(array $attributes = array()) {
        // Check whether meta has been overridden by a subclass
        if (!is_null($this->meta) && count($this->meta) > 0) {
            $this->processMeta($this->meta);
        }
        // We need to call the Eloquent constructor, or else bad things happen.
        parent::__construct($attributes);
    }

    /**
     * Sets the meta attribute, and processes through it to set all the relevant arrays.
     * @param array $meta
     */
    public function setMeta(array $meta = array()) {
        $this->meta = $meta;
        $this->processMeta($meta);
    }

    /**
     * Sets all the relevant arrays denoted within meta.
     * @param array $meta
     */
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