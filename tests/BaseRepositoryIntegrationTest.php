<?php

use Illuminate\Database\Eloquent\Builder;
use Tests\Integration\Item;

class BaseRepositoryIntegrationTest extends PHPUnit_Framework_TestCase
{
    /** @var  Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel */
    protected $model;
    /** @var  \Mockery\MockInterface */
    protected $validator;
    /** @var  \Depotwarehouse\Toolbox\DataManagement\Configuration */
    protected $configuration;

    /** @var  array */
    protected $items;

    protected $oitems;
    protected $titems;

    /** @var  Illuminate\Database\Capsule\Manager */
    protected $capsule;

    public function setUp()
    {
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
        $this->configuration->setInclude(5);
        $this->configuration->setPagination(2, "page");
    }

    private function createAndSeedDatabase()
    {
        // Perform the migration TODO move this somewhere else
        $this->capsule->getConnection('default')->getSchemaBuilder()->dropIfExists('titems');
        $this->capsule->getConnection('default')->getSchemaBuilder()->dropIfExists('oitems');
        $this->capsule->getConnection('default')->getSchemaBuilder()->dropIfExists('items');
        $this->capsule->getConnection('default')->getSchemaBuilder()->create('items', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
        $this->capsule->getConnection('default')->getSchemaBuilder()->create('oitems', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->string('title');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
        });
        $this->capsule->getConnection('default')->getSchemaBuilder()->create('titems', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->integer('oitem_id')->unsigned();
            $table->string('slug');
            $table->timestamps();

            $table->foreign('oitem_id')->references('id')->on('oitems');
        });

        $this->capsule->bootEloquent();

        // Seed with some data
        $this->items = [
            ['name' => "Item One", "description" => "First Item"],
            ['name' => "Item Two", "description" => "Second Item"],
            ['name' => "Item Three", "description" => "Third Item"],
        ];
        $this->oitems = [
            ['item_id' => 1, 'title' => 'Other Item One']
        ];
        $this->titems = [
            ['oitem_id' => 1, 'slug' => 'Cool Item']
        ];

        Item::create($this->items[0]);
        Item::create($this->items[1]);
        Item::create($this->items[2]);
        \Tests\Integration\OtherItem::create($this->oitems[0]);
        \Tests\Integration\ThirdItem::create($this->titems[0]);
    }

    public function tearDown()
    {
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

    public function testSetConfiguration()
    {
        $configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();

        $repository = new BaseRepository($this->model, $this->validator);

        $repository->setConfiguration($configuration);
        $this->assertAttributeEquals($configuration, 'configuration', $repository);
    }

    public function testGetAll()
    {
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

    public function testPaginate()
    {
        $this->createAndSeedDatabase();
        $repository = new BaseRepository($this->model, $this->validator);
        $pages = $repository->paginate();
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $pages);
        $this->assertEquals(3, $pages->getTotal(), "Number of items total is {$pages->getTotal()}");
        $this->assertEquals(2, $pages->count(), "Number of items on this page is {$pages->count()}");
        $this->assertEquals(2, $pages->getLastPage());
        $this->assertEquals(1, $pages->getCurrentPage());
        $items = $pages->getCollection();

        for ($i = 0; $i < 2; $i++) {
            $item = $items->get($i);
            $this->assertEquals($i + 1, $item->id);
            $this->assertEquals($this->items[$i]['name'], $item->name);
            $this->assertEquals($this->items[$i]['description'], $item->description);
        }
    }

    public function testPaginateWithCurrentPageSet()
    {
        $_GET["page"] = 2;
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->paginate();
        $this->assertEquals(2, $pages->getCurrentPage());
        unset($_GET["page"]);
    }

    public function testPaginateWithCurrentPageTooLarge()
    {
        $_GET["page"] = 10;
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->paginate();
        $last_page = $pages->getLastPage();
        $this->assertEquals(2, $last_page);
        $this->assertEquals($last_page, $pages->getCurrentPage());
        unset($_GET["page"]);
    }

    public function test_it_can_order_by_a_column_descending()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);
        $pages = $repository->orderBy('name', true);

        $this->assertEquals("Item Two", $pages->offsetGet(0)->name);
        $this->assertEquals("Item Three", $pages->offsetGet(1)->name);
    }

    public function test_it_can_order_by_a_column_ascending()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);
        $pages = $repository->orderBy('name', false);

        $this->assertEquals("Item One", $pages->offsetGet(0)->name);
        $this->assertEquals("Item Three", $pages->offsetGet(1)->name);
    }


    public function testFilterWithNoArguments()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->filter();
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $pages);
        $this->assertEquals(3, $pages->getTotal());
    }

    public function testFilterWithRegularArgument()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->filter([
            'name' => "two"
        ]);
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $pages);
        $this->assertEquals(1, $pages->getTotal());
    }

    public function testFilterWithRelationshipIncludeArgument()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->filter([
            'oitem:title' => 'One'
        ]);
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $pages);
        $this->assertEquals(1, $pages->getTotal());
        $item = $pages->offsetGet(0);
        $this->assertEquals(1, $item->id);
        $this->assertEquals($this->items[0]["name"], $item->name);
    }

    public function testFilterWithClasspathIncludeArgument()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $pages = $repository->filter([
            'Tests\Integration\OtherItem:title' => 'One'
        ]);
        $this->assertInstanceOf('Illuminate\Pagination\Paginator', $pages);
        $this->assertEquals(1, $pages->getTotal());
        $item = $pages->offsetGet(0);
        $this->assertEquals(1, $item->id);
        $this->assertEquals($this->items[0]["name"], $item->name);
    }

    public function testFilterPostFilter()
    {
        $this->createAndSeedDatabase();
        $repository = new BaseRepository($this->model, $this->validator);

        $postFilter = function (Builder $builder) {
            $builder->where('name', 'like', '%two%');
        };

        // We test with no filters, so it's easy to check the result.
        $pages = $repository->filter([], $postFilter);

        $this->assertEquals(1, $pages->getTotal());
        $item = $pages->offsetGet(0);
        $this->assertEquals(2, $item->id);
        $this->assertEquals($this->items[1]['name'], $item->name);
    }

    public function testSearch()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $found = $repository->search(["other"]);

        $this->assertEquals(1, $found->getTotal());
        $item = $found->offsetGet(0);
        $this->assertInstanceOf('Tests\Integration\Item', $item);
        $this->assertEquals(1, $item->id);
    }

    public function testSearchNoArgs()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $found = $repository->search([]);
        $this->assertEquals(3, $found->getTotal());
    }

    public function testFindSingleItem()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $item = $repository->find(1);
        $this->assertEquals($this->items[0]['name'], $item->name);
        $this->assertEquals($this->items[0]['description'], $item->description);
    }

    public function testFindMultipleItems()
    {
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

    public function testFindItemDoesNotExist()
    {
        $this->createAndSeedDatabase();
        $repository = new BaseRepository($this->model, $this->validator);

        try {
            $repository->find(99);
            $this->fail("Exception should be thrown");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertEquals('Tests\Integration\Item', $exception->getModel());
        }
    }

    public function testCreateSuccessfully()
    {
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

    public function testCreateWithValidationErrors()
    {
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

    public function testUpdate()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);
        $name = 'Count Updateula';

        $this->validator->shouldReceive('updateValidate');
        $repository->update(2, ['name' => $name]);

        $item = $repository->find(2);
        $this->assertEquals($name, $item->name);
    }

    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\ValidationException
     */
    public function testUpdateWithValidationErrors()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);
        $name = 'Count Updateula';

        $this->validator->shouldReceive('updateValidate')->andThrow('\Depotwarehouse\Toolbox\Exceptions\ValidationException');
        $repository->update(2, ['name' => $name]);

        $count = Item::where('name', $name)->count();
        $this->assertEquals(0, $count, "There should not be any item in the database matching this name");
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testUpdateDoesNotExist()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);
        $name = 'Count Updateula';
        $description = 'New Item';

        $this->validator->shouldReceive('validate');
        $repository->update(6, ['name' => $name, 'description' => $description]);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDestroy()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);

        $count = $repository->destroy(1);
        $this->assertEquals(1, $count);

        // Should not be able to find it now.
        $repository->find(1);
    }

    public function testDestroyDoesNotExist()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);

        $count = $repository->destroy(17);
        $this->assertEquals(0, $count);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDestroySomeExist()
    {
        $this->createAndSeedDatabase();

        $repository = new BaseRepository($this->model, $this->validator);

        $count = $repository->destroy([1, 17]);
        $this->assertEquals(1, $count);

        // Should not be able to find it.
        $repository->find(1);
    }

    public function testGetSearchableFields()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        // Test without related models
        $fields = $repository->getSearchableFields(null, false);
        $this->assertInternalType("array", $fields);
        $this->assertEquals(2, count($fields));
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);

        // Test with related models
        // Should return distinct lists to a maximum depth of 5.
        $fields = $repository->getSearchableFields(null, true);
        $this->assertInternalType("array", $fields);
        $this->assertEquals(4, count($fields));
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);
        $this->assertContains('Tests\Integration\OtherItem:title', $fields);
        $this->assertContains('Tests\Integration\OtherItem:Tests\Integration\ThirdItem:slug', $fields);
    }

    public function testGetSearchableFieldsStopsAtDepth()
    {
        $repository = new BaseRepository($this->model, $this->validator);
        $configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
        $configuration->setInclude(1);
        $repository->setConfiguration($configuration);

        $fields = $repository->getSearchableFields(null, true);
        $this->assertInternalType("array", $fields);
        $this->assertEquals(3, count($fields));
        $this->assertContains('name', $fields);
        $this->assertContains('description', $fields);
        $this->assertContains('Tests\Integration\OtherItem:title', $fields);
    }


    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage The requested class: Tests\Integration\ItemInterface is not instantiable
     */
    public function testGetSearchableFieldsWithUninstantiableRelatedModel()
    {
        $model = new \Tests\Integration\ItemUninstantiableRelated();
        $repository = new BaseRepository($model, $this->validator);

        $fields = $repository->getSearchableFields();
    }


    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage The requested class: Tests\Integration\NotFoundClass does not exist
     */
    public function testGetSearchableFieldsWithInvalidRelatedModel()
    {
        $model = new \Tests\Integration\ItemNotFoundRelated();
        $repository = new BaseRepository($model, $this->validator);

        $fields = $repository->getSearchableFields();
    }

    public function testGetUpdateable()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $updateable = $repository->getUpdateableFields();
        $this->assertEquals(2, count($updateable));
        $this->assertContains("name", $updateable);
        $this->assertContains("description", $updateable);
    }

    public function testGetFillable()
    {
        $repository = new BaseRepository($this->model, $this->validator);

        $fillable = $repository->getUpdateableFields();
        $this->assertEquals(2, count($fillable));
        $this->assertContains("name", $fillable);
        $this->assertContains("description", $fillable);
    }
}

class BaseRepository extends \Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepositoryAbstract
{
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
        if (is_null($this->configuration)) {
            $this->configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
        }
    }

}

