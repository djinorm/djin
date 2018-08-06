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
        $this->assertHydrated(null, null, $this->getMapperAllowNull(false));
        $this->assertHydrated(null, '', $this->getMapperAllowNull(false));
        $this->assertHydrated(self::IPv4, self::IPv4, $this->getMapperAllowNull(false));
        $this->assertHydrated(self::IPv6, self::IPv6, $this->getMapperAllowNull(false));

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull(false));
    }

    public function testHydrateAsBinary()
    {
        $this->assertHydrated(null, null, $this->getMapperAllowNull(true));
        $this->assertHydrated(null, '', $this->getMapperAllowNull(true));
        $this->assertHydrated(self::IPv4, inet_pton(self::IPv4), $this->getMapperAllowNull(true));
        $this->assertHydrated(self::IPv6, inet_pton(self::IPv6), $this->getMapperAllowNull(true));

        $this->expectException(HydratorException::class);
        $this->assertHydrated(null, null, $this->getMapperDisallowNull(true));
    }

    public function testExtract()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull(false));
        $this->assertExtracted(null, '', $this->getMapperAllowNull(false));
        $this->assertExtracted(self::IPv4, self::IPv4, $this->getMapperAllowNull(false));
        $this->assertExtracted(self::IPv6, self::IPv6, $this->getMapperAllowNull(false));

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull(false));
    }

    public function testExtractAsBinary()
    {
        $this->assertExtracted(null, null, $this->getMapperAllowNull(true));
        $this->assertExtracted(null, '', $this->getMapperAllowNull(true));
        $this->assertExtracted(inet_pton(self::IPv4), self::IPv4, $this->getMapperAllowNull(true));
        $this->assertExtracted(inet_pton(self::IPv6), self::IPv6, $this->getMapperAllowNull(true));

        $this->expectException(ExtractorException::class);
        $this->assertExtracted(null, null, $this->getMapperDisallowNull(true));
    }

    protected function getMapperAllowNull($asBinary): IpAddressMapper
    {
        return new IpAddressMapper('value', $asBinary, true);
    }

    protected function getMapperDisallowNull($asBinary): IpAddressMapper
    {
        return new IpAddressMapper('value', $asBinary, false);
    }
}
