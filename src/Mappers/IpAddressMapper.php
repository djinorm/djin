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

    /**
     * @var bool
     */
    protected $isBinary;

    public function __construct($modelProperty, $storeAsBinary = true, $allowNull = false, $dbAlias = null)
    {
        $this->modelProperty = $modelProperty;
        $this->isBinary = $storeAsBinary;
        $this->allowNull = $allowNull;
        $this->dbAlias = $dbAlias ?? $modelProperty;
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
        $column = $this->getDbAlias();

        if (!isset($data[$column]) || $data[$column] === '') {
            if ($this->isNullAllowed()) {
                RepoHelper::setProperty($object, $this->getModelProperty(), null);
                return null;
            }
            throw $this->nullHydratorException('IP address', $object);
        }

        $ip = $this->isBinary ? inet_ntop($data[$column]) : $data[$column];

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new HydratorException(sprintf('Trying to hydrate invalid IP address "%s" in %s',
                $data[$column],
                $this->getDescription($object)
            ));
        }

        RepoHelper::setProperty($object, $this->getModelProperty(), $ip);
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
        $ip = RepoHelper::getProperty($object, $this->getModelProperty());

        if ($ip === null || $ip === '') {
            if ($this->isNullAllowed() == false) {
                throw $this->nullExtractorException('IP address', $object);
            }
            return [
                $this->getDbAlias() => null
            ];
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new ExtractorException(sprintf('Trying to extract invalid IP address "%s" in %s',
                $ip,
                $this->getDescription($object)
            ));
        }

        return [
            $this->getDbAlias() => $this->isBinary ? inet_pton($ip) : $ip,
        ];
    }

}