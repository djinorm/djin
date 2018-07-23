<?php
/**
 * Created for DjinORM.
 * Datetime: 27.10.2017 15:57
 * @author Timur Kasumov aka XAKEPEHOK
 */


namespace DjinORM\Djin\Mappers\Handler;


use DjinORM\Djin\Mappers\MapperInterface;
use DjinORM\Djin\Model\ModelInterface;

interface MappersHandlerInterface
{

    /**
     * @return string class name of mapped object
     */
    public function getModelClassName(): string;

    /**
     * @return MapperInterface[]
     */
    public function getMappers(): array;

    /**
     * @param array $data
     * @param object|null $object
     * @return mixed
     */
    public function hydrate(array $data, $object = null);


    /**
     * @param ModelInterface $model
     * @return array
     */
    public function extract($model): array;


    /**
     * @return string[] representation mappers as dot-notation. For example, we have UserModel with id and email, and
     * nested Profile witch contain firstName and lastName values. It can be represented like
     * [
     *      'id' => 'user_id',
     *      'email' => 'email',
     *      'profile.firstName' => 'profile.first_name',
     *      'profile.lastName' => 'profile.last_name',
     * ]
     */
    public function getModelPropertiesToDbAliases(): array;


    /**
     * @see getModelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function getModelPropertyToDbAlias(string $property): ?string;


    /**
     * @return string[] representation mappers as dot-notation. For example, we have UserModel with id and email, and
     * nested Profile witch contain firstName and lastName values. It can be represented like
     * [
     *      'id' => 'user_id',
     *      'email' => 'email',
     *      'profile.first_name' => 'profile.firstName',
     *      'profile.last_name' => 'profile.lastName',
     * ]
     */
    public function getDbAliasesToModelProperties(): array;


    /**
     * @see getModelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function getDbAliasToModelProperty(string $property): ?string;

    /**
     * This method allow you to get mapper by model property name
     * @param string $property - model property. Can be nested, for example: profile.firstName
     * @return MapperInterface|null
     */
    public function getMapperByModelProperty(string $property): ?MapperInterface;

    /**
     * This method allow you to get mapper by db alias name
     * @param string $dbAlias - can be nested, for example: profile.first_name
     * @return MapperInterface|null
     */
    public function getMapperByDbAlias(string $dbAlias): ?MapperInterface;

}