<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 20:37
 */

namespace DjinORM\Djin\Manager;


use DI\ContainerBuilder;
use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Mock\TestModel;
use DjinORM\Djin\Mock\TestModelSecondRepository;
use DjinORM\Djin\Mock\TestSecondModel;
use DjinORM\Djin\Mock\TestStubModel;
use DjinORM\Djin\Mock\TestModelRepository;
use PHPUnit\Framework\TestCase;

class ModelManagerTest extends TestCase
{

    /** @var ModelManager */
    public $manager;

    /** @var TestModelRepository */
    public $repository;

    public $container;

    private $callbacks = [];

    public function setUp()
    {
        $this->container = $container = ContainerBuilder::buildDevContainer();
        $this->manager = new ModelManager(
            $this->container,
            function () {$this->callbacks['beforeCommit'] = true;},
            function () {$this->callbacks['afterCommit'] = true;},
            function () {$this->callbacks['errorCommit'] = true;}
        );
        $this->manager->setModelRepository(TestModelRepository::class);
        $this->manager->setModelRepository(TestModelSecondRepository::class);
        $this->repository = $this->manager->getModelRepository(TestModel::class);
    }

    public function testGetConfig()
    {
        $this->assertEquals([
            TestModel::class => TestModelRepository::class,
            TestSecondModel::class => TestModelSecondRepository::class,
        ], $this->manager->getConfig());
    }

    public function testSetModelConfigModelsArray()
    {
        $manager = new ModelManager($this->container);
        $manager->setModelRepository(TestModelRepository::class, [TestModel::class, TestSecondModel::class]);

        $this->assertInstanceOf(TestModelRepository::class, $manager->getModelRepository(TestModel::class));
        $this->assertInstanceOf(TestModelRepository::class, $manager->getModelRepository(TestSecondModel::class));
    }

    public function testGetModelRepositoryByClassName()
    {
        $repository = $this->manager->getModelRepository(TestModel::class);
        $this->assertInstanceOf(TestModelRepository::class, $repository);
        return $repository;
    }

    public function testGetModelRepositoryByObject()
    {
        $model = new TestModel();
        $repository = $this->manager->getModelRepository($model);
        $this->assertInstanceOf(TestModelRepository::class, $repository);
        return $repository;
    }

    public function testGetModelRepositoryNotFound()
    {
        $this->expectException(UnknownModelException::class);
        /** @noinspection PhpUndefinedClassInspection */
        $this->manager->getModelRepository(ErrorException::class);
    }

    public function testGetModelRepository()
    {
        $this->assertEquals($this->repository, $this->manager->getModelRepository(TestModel::class));
    }

    public function testGetRepositoryByModelName()
    {
        $this->assertEquals($this->repository, $this->manager->getRepositoryByModelName(TestModel::getModelName()));
    }

    public function testGetRepositoryByModelNameNotFound()
    {
        $this->expectException(UnknownModelException::class);
        $this->assertEquals($this->repository, $this->manager->getRepositoryByModelName('qwerty'));
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
        $this->assertEquals(1, $this->manager->delete($model));
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

        $newModel_1 = new TestModel();
        $newModel_2 = new TestSecondModel();
        $this->manager->persists($newModel_1, $newModel_2);

        $this->assertFalse($newModel_1->getId()->isPermanent());
        $this->assertFalse($newModel_2->getId()->isPermanent());

        $this->assertArrayNotHasKey('beforeCommit', $this->callbacks);
        $this->assertArrayNotHasKey('afterCommit', $this->callbacks);
        $this->assertArrayNotHasKey('errorCommit', $this->callbacks);

        $this->manager->commit();

        $this->assertArrayHasKey('beforeCommit', $this->callbacks);
        $this->assertArrayHasKey('afterCommit', $this->callbacks);
        $this->assertArrayNotHasKey('errorCommit', $this->callbacks);

        $this->assertTrue($newModel_1->getId()->isPermanent());
        $this->assertTrue($newModel_2->getId()->isPermanent());
        $this->assertEquals(0, $this->manager->persists());

        $model = new TestStubModel();
        $this->assertEquals(0, $this->manager->delete($model));
    }

    public function testCommitErrorException()
    {
        $model = new TestSecondModel();
        $this->manager->persists($model);

        /** @var TestModelSecondRepository $repo */
        $repo = $this->manager->getModelRepository($model);
        $repo->throwExceptionOnSave(true);

        $this->assertArrayNotHasKey('beforeCommit', $this->callbacks);
        $this->assertArrayNotHasKey('afterCommit', $this->callbacks);
        $this->assertArrayNotHasKey('errorCommit', $this->callbacks);

        try {
            $this->manager->commit();
        } catch (\Exception $exception) {
            $this->assertArrayHasKey('beforeCommit', $this->callbacks);
            $this->assertArrayNotHasKey('afterCommit', $this->callbacks);
            $this->assertArrayHasKey('errorCommit', $this->callbacks);
        }
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
        $manager->setModelRepository(TestModelSecondRepository::class, TestSecondModel::class);
        $repo_1 = $manager->getModelRepository(TestModel::class);
        $repo_2 = $manager->getModelRepository(TestSecondModel::class);
        $repo_1->findById(1);
        $repo_2->findById(1);
        $this->assertEquals(0, $repo_1->freeUpMemory());
        $this->assertEquals(0, $repo_2->freeUpMemory());
        $manager->freeUpMemory();
        $this->assertEquals(2, $repo_1->freeUpMemory());
        $this->assertEquals(2, $repo_2->freeUpMemory());
    }

}