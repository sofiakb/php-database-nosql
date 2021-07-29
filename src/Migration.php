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
        File::ensureDirectoryExists(self::path());
        $files = File::files(self::path());
        
        $dirs = File::directories(self::path());

        foreach ($dirs as $dir) {
            $files = array_merge($files, collect(File::files($dir->getPath() . DIRECTORY_SEPARATOR . $dir->getName()))->map(fn($item) => toObject(['name' => $item->getName(), 'path' => $item->getPath(), 'connection' => $dir->getName()]))->toArray());
        }

        $migrations = [];
        foreach ($files as $file) {
            if (!method_exists($file, 'getPath')) {
                $path = $file->path;
                $name = $file->name;
            } else {
                $path = $file->getPath();
                $name = $file->getName();
            }

            require $path;
            $filename = explode('--', File::name($name), 2);
            $filename = $filename[1] ?? $filename[0];

            $class = strtoupper($file->connection ?? '') . str_replace(' ', '', ucwords(str_replace('_', ' ', $filename)));
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
