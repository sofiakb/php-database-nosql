<?php


namespace Sofiakb\Database\NoSQL\Schema\Attributes;


use Sofiakb\Database\NoSQL\Tools\Helpers;

class Type
{
    /**
     * @var string $name
     */
    private string $name;
    
    /**
     * @var string $column
     */
    private string $column;
    
    /**
     * @var int $length
     */
    private int $length;
    
    /**
     * @var mixed $default
     */
    private $default;
    
    /**
     * @var bool $nullable
     */
    private bool $nullable;
    
    /**
     * @var bool $primary
     */
    private bool $primary;
    
    /**
     * @var bool $unique
     */
    private bool $unique;
    
    /**
     * @var bool $increment
     */
    private bool $increment;
    
    /**
     * @var bool $sizeable
     */
    private bool $sizeable = true;
    
    /**
     * @var string $defaultValue
     */
    private string $defaultValue = 'sofiakb__default--value';
    
    
    public function __construct(string $name, string $column, int $length = 0)
    {
        $this->name = $name;
        $this->column = $column;
        $this->length = $length;
        
        $this->nullable = false;
        $this->primary = false;
        $this->unique = false;
        $this->increment = false;
        
        $this->default = $this->defaultValue;
        
        if ($length === 0)
            $this->setSizeable(false);
    }
    
    /**
     * Edit nullable value.
     *
     * @return $this
     */
    public function nullable(): Type
    {
        $this->nullable = true;
        if ($this->default === $this->defaultValue)
            $this->default(null);
        return $this;
    }
    
    /**
     * Edit length value.
     *
     * @return $this
     */
    public function length(int $length): Type
    {
        $this->length = $length;
        return $this;
    }
    
    /**
     * Get nullable value.
     *
     * @return bool
     */
    public function getNullable(): bool
    {
        return $this->nullable;
    }
    
    /**
     * Edit primary value.
     *
     * @return $this
     */
    public function primary(): Type
    {
        $this->primary = true;
        $this->default($this->defaultValue);
        return $this;
    }
    
    /**
     * Get primary value.
     *
     * @return bool
     */
    public function getPrimary(): bool
    {
        return $this->primary;
    }
    
    /**
     * Edit unique value.
     *
     * @return $this
     */
    public function unique(): Type
    {
        $this->unique = true;
        return $this;
    }
    
    /**
     * Edit increment value.
     *
     * @return $this
     */
    public function increment(): Type
    {
        $this->increment = true;
        return $this;
    }
    
    /**
     * Get unique value.
     *
     * @return bool
     */
    public function getUnique(): bool
    {
        return $this->unique;
    }
    
    /**
     * Edit default value.
     *
     * @param $default
     * @return $this
     */
    public function default($default): Type
    {
        $this->default = $default;
        return $this;
    }
    
    /**
     * Get the default value.
     *
     * @return mixed|string
     */
    public function getDefault()
    {
        return $this->default;
    }
    
    /**
     * @param bool $sizeable
     */
    public function setSizeable(bool $sizeable): void
    {
        $this->sizeable = $sizeable;
    }
    
    public static function what($type)
    {
        return class_exists(($class = __NAMESPACE__ . '\\' . ucfirst($type))) ? $class : null;
    }
    
    public static function check($value): bool
    {
        return true;
    }
    
    public function compile()
    {
        return Helpers::toObject([
            'name'      => $this->name,
            'column'    => $this->column,
            'length'    => $this->length,
            'default'   => ($this->default === $this->defaultValue) ? '' : $this->default,
            'increment' => $this->increment,
            'primary'   => $this->primary,
            'unique'    => $this->unique,
            'nullable'  => $this->nullable
        ]);
    }
    
}