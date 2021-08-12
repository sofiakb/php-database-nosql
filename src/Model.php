<?php
/**
 * This file contains Model class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 26/07/2021
 * Time: 10:41
 */

namespace Sofiakb\Database\NoSQL;

use Exception;
use JsonSerializable;
use ReflectionClass;
use ReflectionException;
use Sofiakb\Support\Traits\ForwardsCalls;
use Tightenco\Collect\Support\Collection;

/**
 * Class Model
 * @package Sofiakb\Database\NoSQL
 * @author Sofiakb <contact.sofiak@gmail.com>
 *
 * @method static int count()
 *
 * @mixin Model
 * @mixin Store
 */
class Model implements JsonSerializable
{
    use ForwardsCalls;
    
    /**
     * @var Model|null $instance
     */
    protected static ?Model $instance = null;
    
    /**
     * @var string|null $dbDirectory
     */
    protected ?string $dbDirectory = null;
    
    /**
     * @var string $defaultDirectory
     */
    protected string $defaultDirectory = 'database';
    
    /**
     * @var string|null $connection
     */
    protected ?string $connection = null;
    
    /**
     * @var string $defaultConnection
     */
    protected string $defaultConnection = 'nosql';
    
    /**
     * @var string|null $table
     */
    protected ?string $table = null;
    
    /**
     * @var Store $table
     */
    protected Store $store;
    
    /**
     * @var array|null $fields
     */
    protected ?array $fields = null;
    
    /**
     * @var string $columnID
     */
    protected string $columnID = '_id';
    
    /**
     * @var int $perFiles
     */
    protected int $perFiles = 50;
    
    /**
     * @var array $attributes
     */
    protected array $attributes;
    
    /**
     * Model constructor.
     */
    public function __construct($attributes = array())
    {
        if ($this->connection) {
            $this->dbDirectory = ($this->dbDirectory ?? (project_path() . DIRECTORY_SEPARATOR . $this->defaultDirectory)) . DIRECTORY_SEPARATOR . $this->connection;
        } else
            $this->dbDirectory = $this->dbDirectory ?? $this->defaultDirectory;
        
        $this->connection = $this->connection ?? $this->defaultConnection;
        
        if (!is_null($attributes))
            foreach ($attributes as $attribute => $value) {
                $this->attributes[$attribute] = $value;
                // $this->{$attribute} = $value;
            }
        
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
            return $this->forwardCallTo($this->table(), $method, $arguments);
        } elseif (strpos($name, 'where') !== false) {
            $method = 'where';
            $arguments = $name !== 'where' ? array_merge([str_replace('where', '', strtolower($name))], $arguments) : $arguments;
            return $this->forwardCallTo($this->table()->getStore(), $method, $arguments);
        } elseif ($name === 'all')
            return $this->table()->getStore()->findAll()->get();
        elseif (method_exists(Store::class, $name) || method_exists(Collection::class, $name)) {
            return $this->forwardCallTo($this->table()->getStore(), $name, $arguments);
        } else throw new Exception("Method [$name] not found in " . static::class);
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if (strpos($name, 'where') !== false) {
            $method = 'where';
            $arguments = $name !== 'where' ? array_merge([str_replace('where', '', strtolower($name))], $arguments) : $arguments;
        } elseif ($name === 'all')
            return static::getInstance()->table()->getStore()->findAll()->get();//else throw new Exception("Method [$name] not found in " . static::class);
        return call_user_func_array(array(static::getInstance()->table(), $method ?? $name), $arguments);
    }
    
    public function __get($name)
    {
        return $this->attributes[$name] ?? ($name === 'id' ? $this->attributes[$this->columnID] ?? null : null);
    }
    
    public function __set($property, $value)
    {
        $this->attributes[$property] = $value;
    }
    
    /**
     * @param string|null $tablename
     * @return $this
     */
    public function table(?string $tablename = null): self
    {
        try {
            if (is_null($tablename)) {
                $tablename = is_null($this->table)
                    ? pluralize(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace([(new ReflectionClass(get_called_class()))->getNamespaceName(), '\\'], '', get_called_class()))))
                    : $this->table;
            }
            $this->table = $tablename;
            $this->store = new Store($this->table, $this->connection, $this->dbDirectory, $this->perFiles, get_called_class(), $this->columnID);
            return $this;
        }
        catch (ReflectionException $exception) {
            return $this;
        }
    }
    
    /**
     * @return array|null
     */
    // public function all(): ?array
    // {
    //     return toObject(self::getInstance()->table()->getStore()->findAll()->get());
    // }
    
    /**
     * @param array|string $columns
     * @return Store
     */
    // public function select($columns = '*'): Store
    // {
    //     return self::getInstance()->table()->getStore()->select($columns);
    // }
    
    /**
     * @param string|null $column
     * @param mixed $operator
     * @param ?mixed $value
     * @return Store
     */
    // public function where(?string $column, $operator, $value = null): Store
    // {
    //     return $this->table()->getStore()->where($column, $operator, $value);
    // }
    
    /**
     * @param $value
     * @return Store
     */
    public static function whereId($value): Store
    {
        $instance = static::getInstance();
        return $instance->table()->getStore()->where($instance->columnID, (int)$value);
    }
    
    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }
    
    /**
     * @return string|null
     */
    public function getDbDirectory(): ?string
    {
        return $this->dbDirectory;
    }
    
    public function jsonSerialize()
    {
        return $this->attributes ?? [];
    }
}