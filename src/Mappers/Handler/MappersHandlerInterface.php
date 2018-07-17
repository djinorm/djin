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
     * @return mixed
     */
    public function hydrate(array $data);


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
    public function modelPropertiesToDbAliases(): array;


    /**
     * @see modelPropertiesToDbAliases()
     * @param string $property
     * @return string
     */
    public function modelPropertyToDbAlias(string $property): string;

}