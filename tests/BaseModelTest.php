<?php
use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;

class BaseModelTest extends PHPUnit_Framework_TestCase {

    /** @var  array */
    protected $meta;

    public function setUp() {
        $capsule = new Illuminate\Database\Capsule\Manager();

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ]);
        $capsule->bootEloquent();

        $this->meta = [
            'id' => [ BaseModel::GUARDED ],
            'name' => [ BaseModel::FILLABLE, BaseModel::SEARCHABLE ],
            'address' => [ BaseModel::FILLABLE, BaseModel::SEARCHABLE, BaseModel::UPDATEABLE ]
        ];
    }

    public function testSetMeta() {
        $baseModel = new BaseModel;
        $baseModel->setMeta($this->meta);
        $this->assertEquals($this->meta, PHPUnit_Framework_Assert::readAttribute($baseModel, 'meta'));
    }

    public function test_it_can_get_meta() {
        $baseModel = new BaseModel;
        $baseModel->setMeta($this->meta);
        $this->assertEquals($this->meta, $baseModel->getMeta());
    }

    public function testProcessMeta() {
        $baseModel = new BaseModel;
        $baseModel->setMeta($this->meta);

        $this->assertAttributeInternalType('array', 'fillable', $baseModel);
        $this->assertAttributeInternalType('array', 'guarded', $baseModel);
        $this->assertAttributeInternalType('array', 'searchable', $baseModel);
        $this->assertAttributeInternalType('array', 'updateable', $baseModel);

        $this->assertAttributeContains('name', 'fillable', $baseModel);
        $this->assertAttributeContains('address', 'fillable', $baseModel);
        $this->assertAttributeNotContains('id', 'fillable', $baseModel);

        $this->assertAttributeContains('id', 'guarded', $baseModel);
        $this->assertAttributeNotContains('name', 'guarded', $baseModel);
        $this->assertAttributeNotContains('address', 'guarded', $baseModel);

    }

    public function testMetaDefaultsEmpty() {
        $baseModel = new BaseModel;

        $this->assertAttributeEmpty('meta', $baseModel);
    }

    public function testGuardedDefaultsAll() {
        $baseModel = new BaseModel;

        $this->assertAttributeContains('*', 'guarded', $baseModel);
    }

    public function testGuardedDisabledWhenAnyMetaPassed() {
        $meta = [
            'id' => [ BaseModel::FILLABLE ]
        ];
        $baseModel = new BaseModel;
        $baseModel->setMeta($meta);

        $this->assertAttributeEmpty('guarded', $baseModel, "Guarded Array Contains values: " . implode(',', $baseModel->guarded));
    }

    public function testMetaOverriddenFromBaseClass() {
        $baseModel = new \Tests\Integration\Item();

        $meta = [
            'id' => [ BaseModel::GUARDED ],
            'name' => [ BaseModel::FILLABLE, BaseModel::SEARCHABLE, BaseModel::UPDATEABLE ],
            'description' => [ BaseModel::FILLABLE, BaseModel::UPDATEABLE, BaseModel::SEARCHABLE ],
            'Tests\Integration\OtherItem:*' => [ BaseModel::SEARCHABLE ]
        ];

        $this->assertAttributeEquals($meta, 'meta', $baseModel);
    }

    public function testGetRelatedModels() {
        $baseModel = new \Tests\Integration\Item();

        $name = $baseModel->getRelationshipName('Tests\Integration\OtherItem');

        $this->assertEquals('oitem', $name);
    }

    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage The requested relationship: Tests\Integration\NotExistItem was not found
     */
    public function testGetRelatedModelsNotExist() {
        $baseModel = new \Tests\Integration\Item();

        $name = $baseModel->getRelationshipName('Tests\Integration\NotExistItem');
    }

    public function testGetRelatedModelsValueExist() {
        $baseModel = new \Tests\Integration\Item();

        $name = $baseModel->getRelationshipName('oitem');
        $this->assertEquals('oitem', $name);
    }
}
