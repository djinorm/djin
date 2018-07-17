<?php
/**
 * Created for DjinORM.
 * Datetime: 31.10.2017 12:16
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DateTimeImmutable;
use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class DatetimeMapperTest extends MapperTestCase
{

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(null, '', $this->getMapperAllowNull());
        $this->assertHydrated(new DateTimeImmutable('2017-11-02'), '2017-11-02', $this->getMapperAllowNull());
        $this->assertHydrated(new DateTimeImmutable('2017-11-02 11:43'), '2017-11-02 11:43', $this->getMapperAllowNull());
        $this->assertHydrated(new DateTimeImmutable('2017-11-02 11:43:15'), '2017-11-02 11:43:15', $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull());
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(null, '', $this->getMapperAllowNull());
        $this->assertExtracted('2017-11-02 00:00:00', new DateTimeImmutable('2017-11-02'), $this->getMapperAllowNull());
        $this->assertExtracted('2017-11-02 11:43:00', new DateTimeImmutable('2017-11-02 11:43'), $this->getMapperAllowNull());
        $this->assertExtracted('2017-11-02 11:43:15', new DateTimeImmutable('2017-11-02 11:43:15'), $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    protected function getMapperAllowNull(): DatetimeMapper
    {
        return new DatetimeMapper('value', 'value', true);
    }

    protected function getMapperDisallowNull(): DatetimeMapper
    {
        return new DatetimeMapper('value', 'value', false);
    }
}
