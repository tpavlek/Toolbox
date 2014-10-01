<?php


use Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepository;

class BaseRepositoryIntegrationTest extends PHPUnit_Framework_TestCase{
    /** @var  Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel */
    protected $model;
    /** @var  \Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator */
    protected $validator;

    public function setUp() {
        $capsule = new Illuminate\Database\Capsule\Manager();

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__.'/test-db.sqlite',
            'prefix' => ''
        ]);
        $capsule->bootEloquent();

        $this->model = new \Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel();
        $this->validator = Mockery::mock('\Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator');
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

    public function tearDown() {
        Mockery::close();
    }
} 