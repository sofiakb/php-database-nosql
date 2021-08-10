<?php
/**
 * This file contains Checker class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 28/07/2021
 * Time: 15:09
 */

namespace Sofiakb\Database\NoSQL\Tools\Checker;


use Sofiakb\Database\NoSQL\Exceptions\StructureException;
use Sofiakb\Database\NoSQL\Exceptions\UniqueConstraintException;
use Sofiakb\Database\NoSQL\Schema\Attributes\Type;
use Sofiakb\Database\NoSQL\Store;

class Checker
{
    
    /**
     * @var array $fields
     */
    public array $fields;
    
    /**
     * @var ?mixed $data
     */
    public $data;
    
    /**
     * @var Store $store
     */
    public Store $store;
    
    /**
     * Checker constructor.
     * @param Store $store
     * @param array $fields
     * @param ?mixed $data
     */
    public function __construct(Store $store, array $fields, $data = null)
    {
        $this->store = new Store($store->getTablename(), $store->getConnection(), $store->getDbDirectory(), $store->getPerFiles(), $store->getClass(), $store->getColumnID());
        $this->fields = $fields;
        $this->data = $data;
    }
    
    /**
     * @param array $updatable
     * @return bool
     * @throws StructureException
     */
    public function update(array $updatable): bool
    {
        $fields = collect($this->fields)->filter(fn($item) => in_array($item['name'], array_keys($updatable)));
        
        foreach ($fields as $field) {
            if (($updatable[$field['name']] === null || $updatable[$field['name']] === 'null'))
                if ($field['nullable'] === false)
                    throw new StructureException($field['name'], $field['column'] . ", got : null");
                else continue;
            elseif (($class = Type::what($field['column'])) === null || ($class::check($updatable[$field['name']]) === false))
                throw new StructureException($field['name'], $field['column']);
        }
        
        $this->unique($updatable);
        
        return true;
    }
    
    /**
     * @param Store $store
     * @param mixed $data
     * @return bool
     * @throws UniqueConstraintException
     */
    public function unique($data = null)
    {
        $uniques = array_filter($this->fields, fn($item) => $item['unique'] ?? false);
        
        $data = $data ?? $this->data;
        
        foreach ($uniques as $field) {
            $column = $field['name'];
            if (isset($data[$column])) {
                $got = $this->store->where($column, $data[$column] ?? null)->get();
                if (count($got ?? []) !== 0)
                    throw new UniqueConstraintException($column);
            }
        }
        return true;
    }
    
    /**
     * @return bool
     */
    public function existence()
    {
        $fieldsKeys = $this->__keys();
        $dataKeys = $this->__keys('data');
        
        $arrayDiff1 = array_diff($fieldsKeys, $dataKeys);
        if (count($arrayDiff1) === 0)
            return true;
        
        $nullables = array_filter($this->fields, fn($item) => $item['nullable'] ?? false);
        $arrayDiff2 = array_diff($arrayDiff1, array_map(fn($item) => $item['name'], $nullables));
        return count($arrayDiff2) === 0;
    }
    
    /**
     * @param string $which
     * @return array
     */
    public function __keys(string $which = 'fields'): array
    {
        if ($which === 'fields')
            return $keys = array_map(fn($item) => $item['name'], $this->fields);
        elseif ($which === 'data')
            return array_keys($this->data);
        return [];
    }
}