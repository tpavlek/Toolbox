<?php

namespace Tests\Integration;

use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;

/**
 * Class Item
 * @package Tests\Integration
 *
 * @property string $name
 */
class Item extends BaseModel {

    protected $meta = [
        'id' => [ self::GUARDED ],
        'name' => [ self::FILLABLE, self::SEARCHABLE, self::UPDATEABLE ],
        'description' => [ self::FILLABLE, self::UPDATEABLE, self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

}