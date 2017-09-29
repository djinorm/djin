<?php
/**
 * @author Timur Kasumov (aka XAKEPEHOK)
 * Datetime: 15.03.2017 15:11
 */

namespace DjinORM\Djin\Model;

use DjinORM\Djin\Id\Id;

interface ModelInterface
{

    public static function getModelName():string;

    public static function getModelIdPropertyName():string;

    public function getId(): Id;

}