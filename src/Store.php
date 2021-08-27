<?php
/**
 * This file contains Store class.
 * Created by PhpStorm.
 * User: Sofiakb <contact.sofiak@gmail.com>
 * Date: 26/07/2021
 * Time: 11:59
 */

namespace Sofiakb\Database\NoSQL;


use Exception;
use Sofiakb\Database\NoSQL\Exceptions\ColumnNotExistsException;
use Sofiakb\Database\NoSQL\Exceptions\StructureException;
use Sofiakb\Database\NoSQL\Exceptions\UniqueConstraintException;
use Sofiakb\Database\NoSQL\Tools\Checker\Checker;
use Sofiakb\Filesystem\Facades\File;
use Sofiakb\Support\Traits\ForwardsCalls;
use Tightenco\Collect\Support\Collection;

/**
 * Class Store
 * @package Sofiakb\Database\NoSQL
 * @author Sofiakb <contact.sofiak@gmail.com>
 *
 * @mixin Collection
 */
class Store
{
    
    use ForwardsCalls;
    
    /**
     * @var string $tablename
     */
    private string $tablename;
    
    /**
     * @var string $connection
     */
    private string $connection;
    
    /**
     * @var string $path
     */
    private string $path;
    
    /**
     * @var string $dataPath
     */
    private string $dataPath = 'data';
    
    /**
     * @var mixed|null $data
     */
    private $data;
    
    /**
     * @var string $statsFile
     */
    private string $statsFile = 'stats.json';
    
    /**
     * @var string|null $statsPath
     */
    private ?string $statsPath;
    
    /**
     * @var string $structureFile
     */
    private string $structureFile = 'structure.json';
    
    /**
     * @var string|null $structurePath
     */
    private ?string $structurePath;
    
    /**
     * @var array $columns
     */
    private array $columns;
    
    /**
     * @var bool $dataSat
     */
    private bool $dataSat = false;
    
    /**
     * @var int $perFiles
     */
    private int $perFiles;
    
    /**
     * @var string $columnID
     */
    private string $columnID;
    
    /**
     * @var string $dbDirectory
     */
    private string $dbDirectory;
    
    /**
     * @var ?int $latestID
     */
    private ?int $latestID = null;
    
    private $class;
    
    /**
     * Store constructor.
     * @param string $tablename
     * @param string $connection
     * @param string $dbDirectory
     * @param int $perFiles
     * @param string $columnID
     */
    public function __construct(string $tablename, string $connection, string $dbDirectory, int $perFiles, $class, string $columnID = '_id')
    {
        $this->tablename = $tablename;
        $this->connection = $connection;
        
        $this->dbDirectory = $dbDirectory;
        
        $this->path = $dbDirectory . DIRECTORY_SEPARATOR . $tablename;
        $this->dataPath = $this->path . DIRECTORY_SEPARATOR . $this->dataPath;
        
        $this->data = null;
        
        $this->perFiles = $perFiles;
        $this->columnID = $columnID;
        
        File::ensureDirectoryExists($this->dataPath);
        
        $this->statsPath = $this->path . DIRECTORY_SEPARATOR . $this->statsFile;
        $this->structurePath = $this->path . DIRECTORY_SEPARATOR . $this->structureFile;
        
        $this->class = $class;
        if (!File::exists($this->statsPath))
            File::put($this->statsPath, $this->toString(['count' => 0, 'latestID' => 0]));
        
        $structureFile = Migration::structure_path() . DIRECTORY_SEPARATOR . ($this->connection === 'nosql' ? '' : $this->connection . DIRECTORY_SEPARATOR) . "{$tablename}.json";
        if ((!File::exists($this->structurePath) && File::exists($structureFile)) || File::lastModified($structureFile) > File::lastModified($this->structurePath))
            File::copy($structureFile, $this->structurePath);
        
    }
    
    /**
     * Find all data.
     *
     * @return $this
     */
    public function findAll($file = null, $withFiles = false): Store
    {
        $files = File::files($this->dataPath);
        
        if ($file)
            $files = array_filter($files, fn($item) => $item->getName() === $file);
        
        $data = [];
        
        foreach ($files as $file) {
            if ($withFiles)
                $data[$file->getName()] = json_decode(File::get($this->dataPath . DIRECTORY_SEPARATOR . $file->getName()));
            else $data = array_merge($data, json_decode(File::get($this->dataPath . DIRECTORY_SEPARATOR . $file->getName())));
        }
        
        $this->setData($data);
        return $this;
    }
    
    /**
     * Return filtered data.
     *
     * @param $item
     * @param $column
     * @param $operator
     * @param $value
     * @return bool
     */
    private function filterData($item, $column, $operator, $value): bool
    {
        // if (!isset($item->$column) && !isset($item->attributes[$column]))
        //     return false;
        $itemValue = $item->$column;
        switch ($operator) {
            case '>':
                return $itemValue > $value;
                break;
            case '>=':
                return $itemValue >= $value;
                break;
            case '<':
                return $itemValue < $value;
                break;
            case '<=':
                return $itemValue <= $value;
                break;
            case '!=':
                return $itemValue != $value;
                break;
            default:
                return $itemValue == $value;
        }
    }
    
    /**
     * * Return negative filtered data.
     *
     * @param $item
     * @param $column
     * @param $operator
     * @param $value
     * @return bool
     */
    private function filterDataNegative($item, $column, $operator, $value): bool
    {
        // if (!isset($item->$column))
        //     return false;
        $itemValue = $item->$column;
        switch ($operator) {
            case '>':
                return $itemValue <= $value;
                break;
            case '>=':
                return $itemValue < $value;
                break;
            case '<':
                return $itemValue >= $value;
                break;
            case '<=':
                return $itemValue > $value;
                break;
            case '!=':
                return $itemValue == $value;
                break;
            default:
                return $itemValue != $value;
        }
    }
    
    /**
     * Find all data where... .
     *
     * @return $this
     * @throws ColumnNotExistsException
     */
    public function findBy(string $column, string $operator, $value, $file = null): Store
    {
        $this->checkStructure($column);
        
        $data = collect($this->data ?? $this->all($file))
            ->filter(fn($item) => $this->filterData($item, $column, $operator, $value))
            ->values();
        
        $this->setData($data);
        
        return $this;
    }
    
    /**
     * @param string $column
     * @param string $operator
     * @param mixed|null $value
     * @return $this
     */
    public function where(string $column, string $operator, $value = null): Store
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return $this->findBy($column, $operator, $value);
    }
    
    /**
     * @param $value
     * @param string $columnID
     * @return $this
     */
    public function whereId($value): Store
    {
        return $this->findBy($this->columnID, '=', $value, ceil((int)$value / $this->perFiles) . '.json');
    }
    
    /**
     * @param string|array $columns
     *
     * @return $this
     */
    public function select($columns = '*'): Store
    {
        if (is_array($columns))
            $this->columns = $columns;
        elseif ($columns === '*')
            $this->columns = [];
        elseif (is_string($columns))
            $this->columns = explode(',', $columns);
        return $this;
    }
    
    /**
     * Get all data.
     *
     * @return mixed|null
     */
    private function all($file = null)
    {
        return $this->findAll($file)->data;
    }
    
    /**
     * @param string $column
     * @param $operator
     * @param null $value
     * @return Collection
     * @throws ColumnNotExistsException
     */
    private function filesMatchWithFilter(string $column, &$operator, &$value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->checkStructure($column);
        
        return collect($this->findAll(null, true)->get())
            ->map(fn($values, $key) => collect($values)->map(fn($item) => toObject([$column => $item->$column ?? null, '__data_file' => $key]))->toArray())
            ->flatten()
            ->filter(fn($item) => $this->filterData($item, $column, $operator, $value))
            ->map(fn($item) => $item->__data_file);
    }
    
    /**
     * @param array $values
     * @return array
     * @throws StructureException
     * @throws UniqueConstraintException
     */
    public function insert(array $values): array
    {
        $this->latestID = null;
        $this->checkBeforeInsert($values);
        
        $backup = $values;
        
        if (isset($values[0]) && is_array($values[0])) ;
        else $values = [$values];
        
        $data = [];
        
        foreach ($values as $item) {
            $file = ceil((int)$item[$this->columnID] / $this->perFiles) . '.json';
            if (!isset($data[$file]))
                $data[$file] = $this->findAll($file)->get();
            $data[$file][] = $item;
        }
        
        $this->latestID(true);
        
        foreach ($data as $file => $datum)
            $this->write($file, collect($datum));
        
        $this->count(true);
        
        return isset($backup[0]) && is_array($backup[0]) ? $values : $values[0];
    }
    
    /**
     * @param array $values
     * @return array
     * @throws StructureException
     * @throws UniqueConstraintException
     */
    public function create(array $values): array
    {
        return $this->insert($values);
    }
    
    /**
     * @param array $updatable
     * @param string $column
     * @param $operator
     * @param null $value
     * @return bool
     * @throws ColumnNotExistsException
     */
    public function updateBy(array $updatable, string $column, $operator, $value = null): bool
    {
        $files = $this->filesMatchWithFilter($column, $operator, $value);
        
        $data = [];
        
        $checker = new Checker($this, json_decode(File::get($this->structurePath), true));
        $checker->update($updatable);
        
        $updatable['updated_at'] = $updatable['updated_at'] ?? today('Y-m-d H:i:s');
        
        foreach ($files as $file)
            $data[$file] = collect($this->findAll($file)->get())
                // ->filter(fn($item) => $this->filterDataNegative($item, $column, $operator, $value))
                ->map(function ($item) use ($column, $operator, $value, $updatable) {
                    if (!$this->filterData($item, $column, $operator, $value))
                        return $item;
                    foreach (array_keys($updatable) as $key) {
                        $item->$key = $updatable[$key];
                    }
                    return $item;
                });
        
        foreach ($data as $file => $datum)
            $this->write($file, $datum);
        
        return true;
    }
    
    /**
     * @param array $updatable
     * @param null $id
     * @return bool
     * @throws ColumnNotExistsException
     */
    public function update(array $updatable, $id = null): bool
    {
        if ($id)
            return $this->updateBy($updatable, $this->columnID, '=', $id);
        else {
            $data = $this->data ?? [];
            foreach ($data as $datum) {
                $columnID = $this->columnID;
                $this->updateBy($updatable, $columnID, '=', $datum->$columnID);
            }
            return true;
        }
    }
    
    /**
     * @param string $column
     * @param $operator
     * @param null $value
     * @return bool
     * @throws ColumnNotExistsException
     */
    public function deleteBy(string $column, $operator, $value = null): bool
    {
        $files = $this->filesMatchWithFilter($column, $operator, $value);
        
        $data = [];
        
        foreach ($files as $file)
            $data[$file] = collect($this->findAll($file)->get())->filter(fn($item) => $this->filterDataNegative($item, $column, $operator, $value));
        
        
        foreach ($data as $file => $datum) {
            $this->write($file, $datum);
        }
        
        return true;
    }
    
    /**
     * @param $id
     * @return bool
     * @throws ColumnNotExistsException
     */
    public function delete($id = null): bool
    {
        if ($id)
            return $this->deleteBy($this->columnID, '=', $id);
        else {
            $data = $this->data ?? [];
            foreach ($data as $datum) {
                $columnID = $this->columnID;
                $this->deleteBy($columnID, '=', $datum->$columnID);
            }
            return true;
        }
    }
    
    /**
     * Get the data value.
     *
     * @return mixed|null
     */
    public function get()
    {
        $data = collect(!$this->dataSat ? $this->all() : $this->data)->map(fn($item) => is_array($item) || $item instanceof \stdClass ? new $this->class($item) : $item);
        
        $this->setData(isset($this->columns) && count($this->columns)
            ? $data->map(fn($item) => collect($item)->only($this->columns)->all())->values()->toArray()
            : $data->toArray());
        
        unset($data);
        
        return is_null($this->data) || count($this->data) === 0 ? null : $this->data;
    }
    
    /**
     * Get the count value.
     *
     * @return int
     */
    public function count(bool $set = false): int
    {
        if ($set === true) {
            $stats = $this->stats();
            $stats['count'] = count($this->all());
            return File::put($this->statsPath, json_encode($stats, JSON_PRETTY_PRINT));
        }
        return count($this->data ?? $this->all());
    }
    
    /**
     * Get the first value.
     *
     * @return mixed
     */
    public function first()
    {
        return ($item = ($this->data ?? $this->all())[0] ?? null) ? new $this->class($item) : null;
    }
    
    /**
     * Truncate all data.
     */
    public function truncate()
    {
        File::deleteDirectory($this->dataPath);
        File::delete($this->statsPath);
    }
    
    /**
     * @throws ColumnNotExistsException
     */
    public function checkStructure($column): bool
    {
        if (File::exists($this->structurePath)) {
            if (collect(json_decode(File::get($this->structurePath)))->map(fn($item) => $item->name)->contains($column))
                return true;
            throw new ColumnNotExistsException($column, $this->tablename);
        } else
            return true;
    }
    
    /**
     * @param null $data
     */
    public function setData($data): void
    {
        $this->data = $data;
        $this->dataSat = true;
    }
    
    /**
     * @param $data
     * @return false|string
     */
    public function toString($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    
    /**
     * @param string $file
     * @param mixed $data
     * @param string $type
     * @return int
     */
    public function write(string $file, $data, string $type = 'data'): int
    {
        if ($type === 'structure')
            $path = $this->structurePath;
        elseif ($type === 'stats')
            $path = $this->statsPath;
        else $path = $this->dataPath;
        
        return File::put($path . DIRECTORY_SEPARATOR . $file, $this->toString($data->values()->toArray()));
    }
    
    /**
     * @return mixed
     */
    public function structure()
    {
        return json_decode(File::get($this->structurePath), true);
    }
    
    /**
     * @return mixed
     */
    public function stats()
    {
        return json_decode(File::get($this->statsPath), true);
    }
    
    /**
     * @param bool $set
     * @return int
     */
    public function latestID(bool $set = false): int
    {
        $stats = $this->stats();
        if ($set === true) {
            if ($this->latestID) {
                $stats['latestID'] = $this->latestID;
                return File::put($this->statsPath, json_encode($stats, JSON_PRETTY_PRINT));
            }
            return 0;
        }
        return (int)($stats['latestID'] ?? 0);
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'where') !== false) {
            $method = 'where';
            $arguments = array_merge([str_replace('where', '', strtolower($name))], $arguments);
            return call_user_func_array(array($this, $method), $arguments);
        } elseif (method_exists(Collection::class, $name)) {
            $data = $this->forwardCallTo(collect($this->data ?? $this->all()), $name, $arguments);
            if (strpos($name, 'groupBy') === false && $data instanceof Collection)
                $data = $data->values();
            if ($data instanceof Collection) {
                $this->setData($data);
                return $this;
            }
            return $data;
        }
        throw new Exception("Method [$name] not found in " . static::class);
    }
    
    /**
     * @param array $values
     * @return bool
     * @throws StructureException
     * @throws UniqueConstraintException
     */
    protected function checkBeforeInsert(array &$values): bool
    {
        if (isset($values[0]) && is_array($values[0])) {
            foreach ($values as $index => $item) {
                if (!$this->checkIndividual($values[$index]))
                    return false;
            }
            return true;
        }
        return $this->checkIndividual($values);
    }
    
    /**
     * @param array $item
     * @return bool
     * @throws StructureException
     * @throws UniqueConstraintException
     */
    protected function checkIndividual(array &$item): bool
    {
        $columnID = $this->columnID;
        
        if (!isset($item[$columnID]))
            $item[$columnID] = ($this->latestID ?? $this->latestID()) + 1;
        
        $this->latestID = $item[$columnID];
        
        $checker = new Checker($this, $this->structure(), $item);
        if (!$checker->existence())
            throw new StructureException();
        foreach ($checker->__keys() as $key) {
            if (!isset($item[$key])) {
                $default = collect($this->structure())->filter(fn($item) => $item['name'] === $key)->first()['default'];
                if ($default === 'null')
                    $default = null;
                $item[$key] = $default;
            }
        }
        
        if (!$checker->unique())
            throw new UniqueConstraintException();
        
        $now = today('Y-m-d H:i:s');
        $item['created_at'] = $now;
        $item['updated_at'] = $now;
        
        return true;
    }
    
    /**
     * @return string
     */
    public function getTablename(): string
    {
        return $this->tablename;
    }
    
    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }
    
    /**
     * @return string
     */
    public function getDbDirectory(): string
    {
        return $this->dbDirectory;
    }
    
    /**
     * @return int
     */
    public function getPerFiles(): int
    {
        return $this->perFiles;
    }
    
    /**
     * @return string
     */
    public function getColumnID(): string
    {
        return $this->columnID;
    }
    
    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }
}