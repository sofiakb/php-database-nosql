<?php
/**
 * This file contains Id class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:32
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


class Id extends Type
{
    public function __construct(string $name = '_id')
    {
        parent::__construct($name, Types::COLUMN_ID);
        $this->primary();
        $this->unique();
        $this->increment();
    }
}