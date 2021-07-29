<?php
/**
 * This file contains Decimal class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:32
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


class Decimal extends Type
{
    public function __construct(string $name)
    {
        parent::__construct($name, Types::COLUMN_FLOAT);
    }
    
    public static function check($value): bool
    {
        return is_numeric($value) && is_float(floatval($value));
    }
}