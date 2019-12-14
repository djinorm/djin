<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 24.03.2017 20:37
 */

namespace DjinORM\Djin\Manager;


use DI\ContainerBuilder;
use DjinORM\Djin\Exceptions\UnknownModelException;
use DjinORM\Djin\Exceptions\NotModelInterfaceException;
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Id\UuidGenerator;
use DjinORM\Djin\Mock\TestModel_1;
use DjinORM\Djin\Mock\TestModelSecondRepository;
use DjinORM\Djin\Mock\TestModel_2;
use DjinORM\Djin\Mock\TestStubModel;
use DjinORM\Djin\Mock\TestModelRepo;
use DjinORM\Djin\Model\Link;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class ModelManagerTest extends TestCase
{

    /** @var ModelManager */
    private $manager;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = new ModelManager();
    }

}