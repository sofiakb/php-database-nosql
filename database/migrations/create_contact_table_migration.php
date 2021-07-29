<?php
/**
 * This file contains CreateContactTableMigration class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 11:55
 */

use Sofiakb\Database\NoSQL\Migration;
use Sofiakb\Database\NoSQL\Schema\Schema;
use Sofiakb\Database\NoSQL\Schema\Table;

class CreateContactTableMigration extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Table $table) {
            $table->id();
            $table->text('name');
            $table->integer('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->text('gender')->length(1)->nullable();
            $table->boolean('active')->default(false)->nullable();
        });
    }
    
    public function down()
    {
        // TODO : doing what when down
    }
}