<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 20:37
 */

namespace DjinORM\Djin\tests\Unit\Manager;


use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Manager\ModelConfig;
use DjinORM\Djin\Manager\ModelManager;
use DjinORM\Djin\Id\MemoryIdGenerator;
use DjinORM\Djin\tests\Mock\TestModel;
use DjinORM\Djin\tests\Mock\TestModelSecondRepository;
use DjinORM\Djin\tests\Mock\TestSecondModel;
use DjinORM\Djin\tests\Mock\TestStubModel;
use DjinORM\Djin\tests\Mock\TestModelRepository;
use PHPUnit\Framework\TestCase;

class ModelManagerTest extends TestCase
{

    /** @var ModelManager */
    public $manager;

    /** @var TestModelRepository */
    public $repository;

    public function setUp()
    {
        $this->manager = new ModelManager(new MemoryIdGenerator());
        $this->manager->setModelConfig(new TestModelRepository(), TestModel::class, new MemoryIdGenerator());
        $this->manager->setModelConfig(new TestModelRepository(), TestSecondModel::class, new MemoryIdGenerator());
        $this->repository = $this->manager->getModelConfig(TestModel::class)->getRepository();
    }

    public function testSetModelConfigByRepoOnly()
    {
        $repo = new TestModelRepository();
        $manager = new ModelManager(new MemoryIdGenerator());
        $manager->setModelConfig($repo);

        $config = $manager->getModelConfig(TestModel::class);
        $this->assertSame($repo, $config->getRepository());
    }

    public function testSetModelConfigModelsArray()
    {
        $repo = new TestModelRepository();
        $manager = new ModelManager(new MemoryIdGenerator());
        $manager->setModelConfig($repo, [TestModel::class, TestSecondModel::class]);

        $this->assertSame($repo, $manager->getModelConfig(TestModel::class)->getRepository());
        $this->assertSame($repo, $manager->getModelConfig(TestSecondModel::class)->getRepository());
    }

    public function testGetModelConfigByClassName()
    {
        $config = $this->manager->getModelConfig(TestModel::class);
        $this->assertInstanceOf(ModelConfig::class, $config);
        return $config;
    }

    public function testGetModelConfigByObject()
    {
        $model = new TestModel();
        $config = $this->manager->getModelConfig($model);
        $this->assertInstanceOf(ModelConfig::class, $config);
        return $config;
    }

    public function testGetModelNotFoundConfig()
    {
        $this->expectException(UnknownModelException::class);
        $this->manager->getModelConfig(ErrorException::class);
    }

    public function testGetModelRepository()
    {
        $this->assertEquals($this->repository, $this->manager->getModelRepository(TestModel::class));
    }

    public function testPersistsAsArray()
    {
        $this->assertEquals(0, $this->manager->persists());
        $model_1 = new TestModel();
        $model_2 = new TestModel();
        $this->manager->persists([$model_1, $model_2]);
        $this->assertEquals(2, $this->manager->persists());
    }

    public function testPersistsAsArguments()
    {
        $this->assertEquals(0, $this->manager->persists());
        $model_1 = new TestModel();
        $model_2 = new TestModel();
        $this->manager->persists($model_1, $model_2);
        $this->assertEquals(2, $this->manager->persists());
    }

    public function testPersistsNotModelInterface()
    {
        $this->expectException(NotModelInterfaceException::class);
        $model_1 = new \stdClass();
        $model_2 = new \stdClass();
        $this->manager->persists($model_1, $model_2);
    }

    public function testPersistsStub()
    {
        $this->assertEquals(0, $this->manager->persists());
        $model_1 = new TestModel();
        $model_2 = new TestStubModel();
        $this->manager->persists($model_1, $model_2);
        $this->assertEquals(1, $this->manager->persists());
    }

    public function testGetPersistedModels()
    {
        $model_1 = new TestModel();
        $model_2 = new TestModel();
        $model_3 = new TestSecondModel();
        $model_4 = new TestStubModel();
        $this->manager->persists($model_1, $model_2, $model_3, $model_4);
        $persisted = $this->manager->getPersistedModels();
        $this->assertCount(2, $persisted);
        $this->assertSame($model_1, $persisted[get_class($model_1)][0]);
        $this->assertSame($model_2, $persisted[get_class($model_2)][1]);
        $this->assertSame($model_3, $persisted[get_class($model_3)][0]);
        $this->manager->commit();
        $this->assertCount(0, $this->manager->getPersistedModels());
    }

    public function testIsModelPersisted()
    {
        $model_1 = new TestModel(1);
        $model_2 = new TestModel(2);

        $this->manager->persists($model_1);
        $this->assertTrue($this->manager->isPersistedModel($model_1));
        $this->assertFalse($this->manager->isPersistedModel($model_2));
    }

    public function testDeletePermanentModel()
    {
        $model = new TestModel();
        $model->getId()->setPermanentId(1);
        $this->assertEquals(1, $this->manager->delete($model));
    }

    public function testDeletePermanentPersistedModel()
    {
        $model = new TestModel();
        $model->getId()->setPermanentId(1);
        $this->manager->persists($model);
        $this->assertEquals(1, $this->manager->delete($model));
    }

    public function testDeleteNotPermanentModel()
    {
        $model = new TestModel();
        $this->manager->persists($model);
        $this->assertEquals(1, $this->manager->persists());
        $this->assertEquals(0, $this->manager->delete($model));
        $this->assertEquals(0, $this->manager->persists());
    }

    public function testDeleteStub()
    {
        $model = new TestStubModel();
        $this->assertEquals(0, $this->manager->delete($model));
    }

    public function testGetPreparedToDeleteModels()
    {
        $model_1 = new TestModel(1);
        $model_2 = new TestModel(2);
        $model_3 = new TestSecondModel(3);
        $model_4 = new TestStubModel();

        $this->manager->delete($model_1);
        $this->manager->delete($model_2);
        $this->manager->delete($model_3);
        $this->manager->delete($model_4);

        $preparedToDelete = $this->manager->getPreparedToDeleteModels();
        $this->assertCount(2, $preparedToDelete);
        $this->assertSame($model_1, $preparedToDelete[get_class($model_1)][0]);
        $this->assertSame($model_2, $preparedToDelete[get_class($model_2)][1]);
        $this->assertSame($model_3, $preparedToDelete[get_class($model_3)][0]);
        $this->manager->commit();
        $this->assertCount(0, $this->manager->getPreparedToDeleteModels());
    }

    public function testIsPreparedToDeleteModel()
    {
        $model_1 = new TestModel(1);
        $model_2 = new TestModel(2);

        $this->manager->delete($model_1);
        $this->assertTrue($this->manager->isPreparedToDeleteModel($model_1));
        $this->assertFalse($this->manager->isPreparedToDeleteModel($model_2));
    }

    public function testCommit()
    {
        $permanentModel = new TestModel();
        $permanentModel->getId()->setPermanentId(1);
        $this->manager->delete($permanentModel);

        $newModel = new TestModel();
        $this->manager->persists($newModel);
        $this->assertFalse($newModel->getId()->isPermanent());

        $this->manager->commit();

        $this->assertTrue($newModel->getId()->isPermanent());
        $this->assertEquals(2, $this->repository->getQueryCount());
        $this->assertEquals(0, $this->manager->persists());

        $model = new TestStubModel();
        $this->assertEquals(0, $this->manager->delete($model));
    }

    public function testGetTotalQueryCount()
    {
        $manager = $this->manager;
        $manager->setModelConfig(new TestModelSecondRepository(), TestSecondModel::class);
        $repo_1 = $manager->getModelRepository(TestModel::class);
        $repo_2 = $manager->getModelRepository(TestSecondModel::class);
        $repo_1->findById(1);
        $repo_1->findById(1);
        $repo_2->findById(1);
        $repo_2->findById(1);
        $this->assertEquals(2, $manager->getTotalQueryCount());
    }

    public function testIsNewModel()
    {
        $isNew = new TestModel();
        $this->assertTrue($this->manager::isNewModel($isNew));

        $notNew = new TestModel();
        $notNew->getId()->setPermanentId(1);
        $this->assertFalse($this->manager::isNewModel($notNew));
    }

    public function testFreeUpMemory()
    {
        $manager = $this->manager;
        $manager->setModelConfig(new TestModelSecondRepository(), TestSecondModel::class);
        $repo_1 = $manager->getModelRepository(TestModel::class);
        $repo_2 = $manager->getModelRepository(TestSecondModel::class);
        $repo_1->findById(1);
        $repo_2->findById(1);
        $this->assertEquals(2, $manager->getTotalQueryCount());
        $manager->freeUpMemory();
        $repo_1->findById(1);
        $repo_2->findById(1);
        $this->assertEquals(4, $manager->getTotalQueryCount());
    }

}