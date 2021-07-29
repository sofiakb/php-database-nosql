<?php
/**
 * This file contains Table class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:49
 */

namespace Sofiakb\Database\NoSQL\Schema;


use Sofiakb\Database\NoSQL\Migration;
use Sofiakb\Database\NoSQL\Schema\Attributes\Type;
use Sofiakb\Database\NoSQL\Schema\Attributes\Types;
use Sofiakb\Filesystem\Facades\File;
use Sofiakb\Support\Traits\ForwardsCalls;

/**
 * Class Table
 * @package Sofiakb\Database\NoSQL\Schema
 * @author Sofiakb <contact.sofiak@gmail.com>
 *
 * @method Type id()
 * @method Type integer($name)
 * @method Type text($name)
 * @method Type json($name)
 * @method Type date($name)
 * @method Type boolean($name)
 *
 * @mixin Type
 */
class Table
{
    use ForwardsCalls;
    
    private string $connection = 'nosql';
    
    private string $table;
    
    /**
     * @var Type[]
     */
    public array $attributes = [];
    
    public function connection(string $connection): Table
    {
        $this->connection = $connection;
        return $this;
    }
    
    public function table(string $table): Table
    {
        $this->table = $table;
        return $this;
    }
    
    public function create(string $table, callable $callback)
    {
        $this->table($table);
        $this->attributes = [];
        $callback($this);
        
        $structure = [];
        
        foreach ($this->attributes as $attribute) {
            $structure[] = $attribute->compile();
        }
        
        $structurePath = Migration::structure_path() . ($this->connection === 'nosql' ? '' : DIRECTORY_SEPARATOR . $this->connection);
        File::ensureDirectoryExists($structurePath);
        
        File::put($structurePath . DIRECTORY_SEPARATOR . "{$table}.json", json_encode($structure, JSON_PRETTY_PRINT));
    }
    
    public function __call($name, $arguments)
    {
        $methods = array_values(Types::all());
        
        if (in_array($name, $methods)) {
            $class = "\\Sofiakb\\Database\\NoSQL\\Schema\\Attributes\\" . ucfirst($name);
            $attribute = isset($arguments[0]) ? new $class($arguments[0]) : new $class();
            $this->attributes[] = $attribute;
            return $attribute;
        }
    }
}