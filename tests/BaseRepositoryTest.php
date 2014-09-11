<?php


use Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepository;

class BaseRepositoryTest extends PHPUnit_Framework_TestCase{
    /** @var  Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel */
    protected $model;
    /** @var  \Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator */
    protected $validator;

    public function setUp() {
        $this->model = Mockery::mock('\Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel');
        $this->validator = Mockery::mock('\Depotwarehouse\Toolbox\DataManagement\Validators\BaseValidator');
    }

    /**
     * Test that variables are properly set by the constructor.
     */
    public function testInstantiation() {
        $repository = new BaseRepository($this->model, $this->validator);

        $this->assertObjectHasAttribute('model', $repository);
        $this->assertAttributeEquals($this->model, 'model', $repository);

        $this->assertObjectHasAttribute('validator', $repository);
        $this->assertAttributeEquals($this->validator, 'validator', $repository);
    }

    /**
     * Test that eloquent ->all() is called. Testing the actual data returned is out of scope for this test.
     */
    public function testGetAll() {
        $repository = new BaseRepository($this->model, $this->validator);

        /** @var \Mockery\Mock $mockModel */
        $mockModel = $this->model;

        $mockModel->shouldReceive('all');

        $repository->all();
    }

    public function tearDown() {
        Mockery::close();
    }
} 