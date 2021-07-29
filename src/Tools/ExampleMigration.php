<?php
/**
 * This file contains ExampleMigration class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 11:55
 */

use Sofiakb\Database\NoSQL\Migration;

use Sofiakb\Database\NoSQL\Schema\Schema;
use Sofiakb\Database\NoSQL\Schema\Table;

class ExampleMigration extends Migration
{
    public function up()
    {
        Schema::create('table', function (Table $table) {
            $table->id();
        });
    }
    
    public function down()
    {
        // TODO : doing what when down
    }
}