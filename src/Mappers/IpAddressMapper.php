<?php
/**
 * Created for DjinORM.
 * Datetime: 02.11.2017 12:22
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Djin\Mappers;


use DjinORM\Djin\Exceptions\ExtractorException;
use DjinORM\Djin\Exceptions\HydratorException;
use DjinORM\Djin\Helpers\RepoHelper;

class IpAddressMapper extends AbstractMapper
{

    public function __construct($modelProperty, $allowNull = false)
    {
        $this->property = $modelProperty;
        $this->allowNull = $allowNull;
    }

    /**
     * @param array $data
     * @param object $object
     * @return null|string
     * @throws HydratorException
     * @throws \ReflectionException
     */
    public function hydrate(array $data, object $object): ?string
    {
        $property = $this->getProperty();

        if (!isset($data[$property]) || $data[$property] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('IP address', $object);
        }

        $ip = $data[$property];

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new HydratorException(sprintf('Trying to hydrate invalid IP address "%s" in %s',
                $data[$property],
                $this->getDescription($object)
            ));
        }

        RepoHelper::setProperty($object, $this->getProperty(), $ip);
        return $ip;
    }

    /**
     * @param object $object
     * @return array
     * @throws ExtractorException
     * @throws \ReflectionException
     */
    public function extract(object $object): array
    {
        $ip = RepoHelper::getProperty($object, $this->getProperty());

        if ($ip === null || $ip === '') {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('IP address', $object);
            }
            return [
                $this->getProperty() => null
            ];
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new ExtractorException(sprintf('Trying to extract invalid IP address "%s" in %s',
                $ip,
                $this->getDescription($object)
            ));
        }

        return [
            $this->getProperty() => $ip,
        ];
    }

}