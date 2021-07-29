<?php
/**
 * This file contains Migration class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 11:24
 */

namespace Sofiakb\Database\NoSQL;


use Sofiakb\Filesystem\Facades\File;

abstract class Migration
{
    /**
     * Get migration path.
     *
     * @return string
     */
    public static function path(): string
    {
        return (strpos(__DIR__, '/vendor') !== false ? explode('/vendor', __DIR__, 2)[0] : getcwd()) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . 'nosql';
    }
    
    /**
     * Get migration path.
     *
     * @return string
     */
    public static function structure_path(): string
    {
        return str_replace(['migrations', '/nosql'], ['structures', ''], Migration::path());
    }
    
    /**
     * Get all migrations as array.
     *
     * @return array
     */
    public static function all(): array
    {
        $files = File::files(self::path());
        
        $migrations = [];
        foreach ($files as $file) {
            require $file->getPath();
            $filename = explode('--', File::name($file->getName()), 2);
            $filename = $filename[1] ?? $filename[2];
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $filename)));
            $migrations[] = new $class;
        }
        return $migrations;
    }
    
    public function up()
    {
    }
    
    public function down()
    {
    }
}
