<?php

namespace Tests\Integration;

use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;

class Item extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    protected $meta = [
        'id' => [ self::GUARDED ],
        'name' => [ self::FILLABLE, self::SEARCHABLE, self::UPDATEABLE ],
        'description' => [ self::FILLABLE, self::UPDATEABLE, self::SEARCHABLE ],
        'Tests\Integration\OtherItem:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

}

class OtherItem extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    protected $meta = [
        'title' => [ self::FILLABLE, self::SEARCHABLE ],
        'Tests\Integration\ThirdItem:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
}

class ThirdItem extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    protected $meta = [
        'slug' => [ self::FILLABLE, self::SEARCHABLE ],
        'Tests\Integration\Item:*' => [ self::SEARCHABLE ],
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
}

class ItemUninstantiableRelated extends BaseModel {

    protected $meta = [
        'Tests\Integration\ItemInterface:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct();
    }

}

class ItemNotFoundRelated extends BaseModel {

    protected $meta = [
        'Tests\Integration\NotFoundClass:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct();
    }

}

interface ItemInterface {

}