<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 20:37
 */

namespace DjinORM\Djin\Manager;


use DjinORM\Djin\Id\IdGeneratorInterface;
use DjinORM\Djin\Id\UuidGenerator;
use DjinORM\Djin\Locker\DummyLocker;
use DjinORM\Djin\Locker\LockerInterface;
use PHPUnit\Framework\TestCase;

class ModelManagerTest extends TestCase
{

    /** @var LockerInterface */
    private $locker;

    /** @var IdGeneratorInterface */
    private $idGenerator;

    private $configManager;

    /** @var ModelManager */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locker = new DummyLocker();
        $this->idGenerator = new UuidGenerator();

        $this->configManager = new ConfigManager();

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

}