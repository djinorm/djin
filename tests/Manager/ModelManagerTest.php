<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 20:37
 */

namespace DjinORM\Djin\Manager;


use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Id\UuidGenerator;
use DjinORM\Djin\Locker\DummyLocker;
use DjinORM\Djin\Locker\LockerInterface;
use DjinORM\Djin\Mock\TestModel_1;
use DjinORM\Djin\Mock\TestModel_2;
use DjinORM\Djin\Mock\TestRepo_1;
use DjinORM\Djin\Mock\TestRepo_2;
use DjinORM\Djin\Model\Link;
use PHPUnit\Framework\TestCase;

class ModelManagerTest extends TestCase
{

    /** @var LockerInterface */
    private $locker;

    /** @var IdGeneratorInterface */
    private $idGenerator;

    /** @var TestRepo_1 */
    private $repo_1;

    /** @var TestRepo_2 */
    private $repo_2;

    /** @var ConfigManager */
    private $configManager;

    /** @var ModelManager */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locker = new DummyLocker();
        $this->idGenerator = new UuidGenerator();

        $this->repo_1 = new TestRepo_1();
        $this->repo_2 = new TestRepo_2();

        $this->configManager = new ConfigManager();

        $this->configManager->add(
            $this->repo_1,
            [TestModel_1::class],
            $this->idGenerator
        );

        $this->configManager->add(
            $this->repo_2,
            [TestModel_2::class],
            $this->idGenerator
        );

        $this->manager = new ModelManager(
            $this->configManager,
            $this->locker,
            function (Commit $commit) {},
            function (Commit $commit) {},
            function (Commit $commit) {}
        );
    }

    public function testGetLocker()
    {
        $this->assertSame($this->locker, $this->manager->getLocker());
    }

    public function testGetModelRepository()
    {
        $this->assertSame(
            $this->repo_1,
            $this->manager->getModelRepository(TestModel_1::class)
        );

        $this->assertSame(
            $this->repo_2,
            $this->manager->getModelRepository(TestModel_2::class)
        );
    }

    public function testFindByLink()
    {
        $link = new Link(TestModel_1::getModelName(), 2);
        $model = $this->manager->findByLink($link);
        $this->assertTrue($link->isFor($model));
    }

    public function testFindByLinks()
    {
        $link_1 = new Link(TestModel_1::getModelName(), 1);
        $link_2 = new Link(TestModel_2::getModelName(), 2);

        $storage = $this->manager->findByLinks([$link_1, $link_2]);
        $this->assertTrue($link_1->isFor($storage[$link_1]));
        $this->assertTrue($link_2->isFor($storage[$link_2]));
    }

    public function testFindByAnyTypeId()
    {
        $model = new TestModel_1(10);
        $this->assertSame(
            $model,
            $this->manager->findByAnyTypeId($model)
        );

        $link = new Link(TestModel_1::getModelName(), 2);
        $model = $this->manager->findByAnyTypeId($link);
        $this->assertInstanceOf(TestModel_1::class, $model);
        $this->assertEquals('2', $model->getId()->toString());

        $id = new Id(1);
        $model = $this->manager->findByAnyTypeId($id, TestModel_1::class);
        $this->assertInstanceOf(TestModel_1::class, $model);
        $this->assertEquals('1', $model->getId()->toString());

        $id = new Id(3);
        $model = $this->manager->findByAnyTypeId($id, TestModel_2::class);
        $this->assertInstanceOf(TestModel_2::class, $model);
        $this->assertEquals('3', $model->getId()->toString());
    }

}