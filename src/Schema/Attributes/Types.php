<?php
/**
 * This file contains Types class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:37
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


class Types
{
    public const COLUMN_INT = 'integer';
    public const COLUMN_FLOAT = 'decimal';
    public const COLUMN_STRING = 'text';
    public const COLUMN_BOOL = 'boolean';
    public const COLUMN_DATE = 'date';
    public const COLUMN_JSON = 'json';
    public const COLUMN_ID = 'id';
    
    /**
     * Get all class's constants.
     *
     * @return array
     */
    public static function all(): array
    {
        return (new \ReflectionClass(self::class))->getConstants();
    }
}