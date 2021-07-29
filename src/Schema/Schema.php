<?php
/**
 * This file contains Schema class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 27/07/2021
 * Time: 14:48
 */

namespace Sofiakb\Database\NoSQL\Schema;


use Exception;
use Sofiakb\Support\Traits\ForwardsCalls;

/**
 * Class Schema
 * @package Sofiakb\Database\NoSQL\Schema
 * @author Sofiakb <contact.sofiak@gmail.com>
 *
 * @method static Table create(string $table, callable $callback)
 *
 * @mixin Table
 */
class Schema
{
    use ForwardsCalls;
    
    /**
     * @var Table $table
     */
    private Table $table;
    
    /**
     * @var Schema|null $instance
     */
    protected static ?Schema $instance = null;
    
    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $this->table = new Table();
    }
    
    /**
     * @return self
     */
    public static function getInstance(): ?self
    {
        $class = get_called_class();
        if (is_null(static::$instance) || !(static::$instance instanceof $class))
            static::$instance = new $class;
        return static::$instance;
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (method_exists(static::class, $name)) {
            $method = $name;
        } elseif (method_exists(Table::class, $name)) {
            return $this->forwardCallTo($this->table, $name, $arguments);
        } else throw new Exception("Method [$name] not found in " . static::class);
        return $this->forwardCallTo($this, $method, $arguments);
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array(static::getInstance(), $name), $arguments);
    }
}