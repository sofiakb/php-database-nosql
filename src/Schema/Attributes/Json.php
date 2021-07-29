<?php
/**
 * This file contains Json class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:32
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


class Json extends Type
{
    public function __construct(string $name)
    {
        parent::__construct($name, Types::COLUMN_JSON);
    }
}