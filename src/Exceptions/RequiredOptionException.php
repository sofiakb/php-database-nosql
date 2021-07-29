<?php


namespace Sofiakb\Database\NoSQL\Exceptions;

use Exception;
use Throwable;

/**
 * Class RequiredOptionException
 * @package Sofiakb\Database\NoSQL\Exceptions
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class RequiredOptionException extends Exception
{
    /**
     * RequiredOptionException constructor.
     * @param string $option
     * @param string $command
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $option, string $command, $code = 0, Throwable $previous = null)
    {
        $message = "{{$option}} option is required for [$command] command.";
        parent::__construct($message, $code, $previous);
    }
}