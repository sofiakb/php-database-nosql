<?php
/**
 * This file contains Boolean class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:32
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


class Boolean extends Type
{
    public function __construct(string $name)
    {
        parent::__construct($name, Types::COLUMN_BOOL);
    }
    
    public static function check($value): bool
    {
        return in_array($value, ['true', 'false', true, false, 0, 1, '0', '1']);
    }
}