<?php


namespace Sofiakb\Database\NoSQL\Exceptions;


use Throwable;

/**
 * Class UniqueConstraintException
 * @package Sofiakb\Database\NoSQL\Exceptions
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class UniqueConstraintException extends \Exception
{
    /**
     * UniqueConstraintException constructor.
     * @param null $column
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($column = null, $code = 0, Throwable $previous = null)
    {
        $message = $column ? sprintf("Duplicate entry value for column `%s`.", $column) : "Duplicate entry value.";
        parent::__construct($message, $code, $previous);
    }

}