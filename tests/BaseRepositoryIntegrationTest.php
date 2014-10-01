<?php


use Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepository;
use Illuminate\Support\Facades\Schema;
use Tests\Integration\Item;

class BaseRepositoryIntegrationTest extends PHPUnit_Framework_TestCase{
    /** @var  Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel */
    protected $model;
    /** @var  \Mockery\MockInterface */
    protected $validator;

    /** @var  array  */
    protected $items;

    public function setUp() {
        $capsule = new Illuminate\Database\Capsule\Manager();

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ], "default");

        // Perform the migration TODO move this somewhere else
        $capsule->getConnection('default')->getSchemaBuilder()->dropIfExists('items');
        $capsule->getConnection('default')->getSchemaBuilder()->create('items', function(\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        $capsule->bootEloquent();

        // Seed with some data
        $this->items = [
            [ 'name' => "Item One", "description" => "First Item"],
            [ 'name' => "Item Two", "description" => "Second Item"],
            [ 'name' => "Item Three", "description" => "Third Item"],
        ];
        Item::create($this->items[0]);
        Item::create($this->items[1]);
        Item::create($this->items[2]);


        $this->model = new Tests\Integration\Item();
        $this->validator = Mockery::mock('\Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator');
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
        $database_item = \Tests\Integration\Item::find($item->id);
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
            $count = \Tests\Integration\Item::where('name', 'unique_mock')->count();
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
        $repository = new BaseRepository($this->model, $this->validator);

        try {
            $repository->find(99);
            $this->fail("Exception should be thrown");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertEquals('Tests\Integration\Item', $exception->getModel());
        }
    }


} 