<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 12:29
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;

use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\TestHelpers\MapperTestCase;

class IpAddressMapperTest extends MapperTestCase
{

    const IPv4 = '127.0.0.1';
    const IPv6 = '2001:db8:11a3:9d7:1f34:8a2e:7a0:765d';

    public function testHydrate()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull());
        $this->assertHydrated(null, '', $this->getMapperAllowNull());
        $this->assertHydrated(self::IPv4, self::IPv4, $this->getMapperAllowNull());
        $this->assertHydrated(self::IPv6, self::IPv6, $this->getMapperAllowNull());

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull(false));
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull());
        $this->assertExtracted(null, '', $this->getMapperAllowNull());
        $this->assertExtracted(self::IPv4, self::IPv4, $this->getMapperAllowNull());
        $this->assertExtracted(self::IPv6, self::IPv6, $this->getMapperAllowNull());

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull());
    }

    protected function getMapperAllowNull(): IpAddressMapper
    {
        return new IpAddressMapper('value', true);
    }

    protected function getMapperDisallowNull(): IpAddressMapper
    {
        return new IpAddressMapper('value', false);
    }
}
