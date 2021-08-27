<?php


namespace Sofiakb\Database\NoSQL\Exceptions;

use Exception;
use Throwable;

/**
 * Class ColumnNotExistsException
 * @package Sofiakb\Database\NoSQL\Exceptions
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class ColumnNotExistsException extends Exception
{
    /**
     * ColumnNotExistsException constructor.
     * @param string $column
     * @param string $table
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $column, string $table, $code = 0, Throwable $previous = null)
    {
        $message = "{{$column}} column not exists in [$table] table.";
        parent::__construct($message, $code, $previous);
    }
}