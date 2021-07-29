<?php


namespace Sofiakb\Database\NoSQL\Exceptions;


use Throwable;

/**
 * Class StructureException
 * @package Sofiakb\Database\NoSQL\Exceptions
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class StructureException extends \Exception
{
    /**
     * StructureException constructor.
     * @param $column
     * @param $expected
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($column = null, $expected = null, $code = 0, Throwable $previous = null)
    {
        $message = $column && $expected
            ? "Item structure [$column] doesn't not comply with database structure. (Expected : $expected)"
            : "Item structure doesn't not comply with database structure.";
        parent::__construct($message, $code, $previous);
    }
    
}