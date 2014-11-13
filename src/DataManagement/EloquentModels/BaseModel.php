<?php

namespace Depotwarehouse\Toolbox\DataManagement\EloquentModels;

use Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException;
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
     * Array containing information about associated relationships.
     *
     * The format of the array is such that keys represent related objects in the system, and the values represent the
     * functions present in the class that are relationship methods to that object.
     * For example, if the class contains a method 'contacts' which is a relationship method to some Contact model
     * then the relatedModels array would contain a link between  the fully qualified class name
     * of the object that represents contacts and 'contacts'. Eg.
     *
     * ```.language-php
     * [
     *      'Vendor\Package\Models\Contact' => 'contacts'
     * ]
     * ```
     *
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
     * Retrieve the raw meta array containing information about this class.
     * @return array
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Gets the name of the relationship method on the class that links to the given class.
     *
     * If the relationship name is passed in, and it exists in the relatedModels array, the relationship name will be returned.
     * @param string $related_object Fully qualified class name, or the relationship name
     * @return string The name of the relationship method on this class
     * @throws InvalidArgumentException If the key is not found in the relatedModels array
     */
    public function getRelationshipName($related_object) {
        if ( ! array_key_exists($related_object, $this->relatedModels)) {
            if (in_array($related_object, $this->relatedModels)) {
                return $related_object;
            }
            throw new InvalidArgumentException("The requested relationship: {$related_object} was not found");
        }

        return $this->relatedModels[$related_object];
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
