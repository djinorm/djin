<?php
/**
 * Created for DjinORM.
 * Datetime: 02.08.2018 15:20
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Model;

use DjinORM\Djin\Mock\TestModel;
use PHPUnit\Framework\TestCase;

class ShadowTest extends TestCase
{

    /** @var Shadow */
    private $shadow;

    /** @var ModelInterface */
    private $model;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new TestModel(1);
        $this->shadow = new Shadow($this->model->getId(), $this->model::getModelName());
    }

    public function testGetId()
    {
        $this->assertSame($this->model->getId(), $this->shadow->getId());
    }

    public function testGetModelName()
    {
        $this->assertEquals($this->model::getModelName(), $this->shadow->getModelName());
    }

    public function testCreateFromModel()
    {
        $this->assertEquals($this->shadow, Shadow::createFromModel($this->model));
    }

}
