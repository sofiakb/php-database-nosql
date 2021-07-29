<?php

namespace Sofiakb\Database\Tests\Models;

use Sofiakb\Database\NoSQL\Model;

/**
 * This file contains Contact class.
 * Created by PhpStorm.
 * User: Sofiane Akbly <sofiane.akbly@gmail.com>
 * Date: 29/07/2021
 * Time: 10:53
 */
class Contact extends Model
{
    protected ?string $dbDirectory = __DIR__;
}