<?php


class BaseRepositoryIntegrationTest extends PHPUnit_Framework_TestCase{
    /** @var  Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel */
    protected $model;
    /** @var  \Mockery\MockInterface */
    protected $validator;
    /** @var  \Depotwarehouse\Toolbox\DataManagement\Configuration */
    protected $configuration;

    /** @var  array  */
    protected $items;

    /** @var  Illuminate\Database\Capsule\Manager */
    protected $capsule;

    public function setUp() {
        $this->capsule = new Illuminate\Database\Capsule\Manager();

        $this->capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ], "default");

        $this->createAndSeedDatabase();

        $this->model = new Item();
        $this->validator = Mockery::mock('\Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator');
        $this->configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
    }

    private function createAndSeedDatabase() {
        // Perform the migration TODO move this somewhere else
        $this->capsule->getConnection('default')->getSchemaBuilder()->dropIfExists('items');
        $this->capsule->getConnection('default')->getSchemaBuilder()->create('items', function(\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        $this->capsule->bootEloquent();

        // Seed with some data
        $this->items = [
            [ 'name' => "Item One", "description" => "First Item"],
            [ 'name' => "Item Two", "description" => "Second Item"],
            [ 'name' => "Item Three", "description" => "Third Item"],
        ];
        Item::create($this->items[0]);
        Item::create($this->items[1]);
        Item::create($this->items[2]);
    }

    public function tearDown() {
        Mockery::close();
    }

    /**
     * Test that variables are properly set by the constructor.
     */
    public function testInstantiation()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $this->assertObjectHasAttribute('model', $repository);
        $this->assertAttributeEquals($this->model, 'model', $repository);

        $this->assertObjectHasAttribute('validator', $repository);
        $this->assertAttributeEquals($this->validator, 'validator', $repository);
    }

    public function testGetAll() {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);

        $items = $repository->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $items);
        $this->assertEquals(3, $items->count());
        for ($i = 0; $i < 3; $i++) {
            $item = $items->get($i);
            $this->assertEquals($i + 1, $item->id);
            $this->assertEquals($this->items[$i]['name'], $item->name);
            $this->assertEquals($this->items[$i]['description'], $item->description);
        }
    }

    public function testCreateSuccessfully() {
        $repository = new BaseRepository($this->model, $this->validator);
        $name = "Wow";
        $description = "Such Description";

        $this->validator->shouldReceive('validate');

        $item = $repository->create([
            'name' => $name,
            'description' => $description
        ]);

        // We pull from the database and make sure the record exists, and matches.
        $database_item = Item::find($item->id);
        $this->assertEquals($name, $database_item->name);
        $this->assertEquals($description, $database_item->description);
    }

    public function testCreateWithValidationErrors() {
        $repository = new BaseRepository($this->model, $this->validator);

        $this->validator->shouldReceive('validate')->andThrow('\Depotwarehouse\Toolbox\Exceptions\ValidationException');
        try {
            $repository->create([
                'name' => "unique_mock",
                "description" => "Mock"
            ]);
        } catch (\Depotwarehouse\Toolbox\Exceptions\ValidationException $exception) {
            $count = Item::where('name', 'unique_mock')->count();
            $this->assertEquals(0, $count, "There should not be any item in the database matching this name");
        }
    }

    public function testFindSingleItem() {
        $repository = new BaseRepository($this->model, $this->validator);

        $item = $repository->find(1);
        $this->assertEquals($this->items[0]['name'], $item->name);
        $this->assertEquals($this->items[0]['description'], $item->description);
    }

    public function testFindMultipleItems() {
        $repository = new BaseRepository($this->model, $this->validator);

        $item = $repository->find("3,1");
        $this->assertInstanceOf('Illuminate\Support\Collection', $item);
        $this->assertEquals(2, $item->count());

        // The result set should be sorted
        $this->assertEquals($this->items[0]['name'], $item->first()->name);
        $this->assertEquals($this->items[0]['description'], $item->first()->description);

        $this->assertEquals($this->items[2]['name'], $item->last()->name);
        $this->assertEquals($this->items[2]['description'], $item->last()->description);
    }

    public function testFindItemDoesNotExist() {
        $this->createAndSeedDatabase();
        $repository = new BaseRepository($this->model, $this->validator);

        try {
            $repository->find(99);
            $this->fail("Exception should be thrown");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertEquals('Item', $exception->getModel());
        }
    }

    public function testGetSearchableFields() {
        $repository = new BaseRepository($this->model, $this->validator);

        // Test without related models
        $fields = $repository->getSearchableFields(false);
        $this->assertInternalType("array", $fields);
        $this->assertEquals(2, count($fields));
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);

        // Test with related models
        // Should return distinct lists to a maximum depth of 5.
        $fields = $repository->getSearchableFields(true);
        var_dump($fields);
        $this->assertInternalType("array", $fields);
        $this->assertEquals(4, count($fields));
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);
        $this->assertContains('oitem:title', $fields);
        //$this->assertContains('oitem:titem:slug', $fields);
    }
}

class BaseRepository extends \Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepositoryAbstract {
    /**
     * Resolves the configuration object of the class.
     *
     * In order to decouple from frameworks, configuration of this class is done through a Configuration object.
     * However, since this class is meant to be overridden, putting Configuration instantiation in the constructor
     * would require significant boilerplate on the part of the user in order to instantiate and explicitly call
     * constructors with a Configuration object.
     *
     * Rather, the user must implement the method to resolve configuration. This method **must** perform the
     * following task:
     * - Check if the passed configuration object is null, if not, set $this->configuration to the passed object
     * - else resolve the configuration in any manner acceptable to the client
     *
     * @return void
     */
    public function resolveConfiguration()
    {
        $this->configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
    }

}

class Item extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    public $relatedModels = [
        'oitem' => 'OtherItem'
    ];

    protected $meta = [
        'id' => [ self::GUARDED ],
        'name' => [ self::FILLABLE, self::SEARCHABLE, self::UPDATEABLE ],
        'description' => [ self::FILLABLE, self::UPDATEABLE, self::SEARCHABLE ],
        'oitem:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }

}

class OtherItem extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    public $relatedModels = [
        'titem' => 'ThirdItem'
    ];
    protected $meta = [
        'title' => [ self::FILLABLE, self::SEARCHABLE ],
        //'titem:*' => [ self::SEARCHABLE ]
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
}

class ThirdItem extends \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel {

    public $relatedModels = [
        'item' => 'Item'
    ];
    protected $meta = [
        'slug' => [ self::FILLABLE, self::SEARCHABLE ],
        'item:*' => [ self::SEARCHABLE ],
    ];

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
    }
}