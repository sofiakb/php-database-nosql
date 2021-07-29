<?php
/**
 * This file contains Date class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:32
 */

namespace Sofiakb\Database\NoSQL\Schema\Attributes;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\ParseErrorException;

class Date extends Type
{
    public function __construct(string $name)
    {
        parent::__construct($name, Types::COLUMN_DATE);
    }
    
    public static function check($value): bool
    {
        try {
            Carbon::parse($value);
            return true;
        }
        catch (ParseErrorException|InvalidFormatException $exception) {
            return false;
        }
    }
}