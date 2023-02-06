<?php
/**
 * This file contains MakeMigrationCommand class
 */

namespace Sofiakb\Database\NoSQL\Console\Commands;


use Exception;
use Sofiakb\Database\NoSQL\Exceptions\RequiredOptionException;
use Sofiakb\Database\NoSQL\Migration;
use Sofiakb\Database\NoSQL\Model;
use Sofiakb\Database\NoSQL\Schema\Attributes\Integer;
use Sofiakb\Filesystem\Facades\File;
use Throwable;

/**
 * Class MakeMigrationCommand
 * Cette classe est chargé de récupérer le fichier
 * à traiter le passer au programme.
 *
 * @package Sofiakb\Database\NoSQL\Console\Commands
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class MakeMigrationCommand extends Command
{
    private ?string $migrationPath;
    
    private const EXAMPLE_CLASS = 'ExampleMigration';
    private const EXAMPLE_CONNECTION_CLASS = 'ExampleConnectionMigration';
    
    /**
     * MakeMigrationCommand constructor.
     * @param array|null $argv
     */
    public function __construct(array $argv = null)
    {
        $this->migrationPath = Migration::path();
        
        File::ensureDirectoryExists($this->migrationPath);
        
        parent::__construct($argv);
    }
    
    /**
     * Handle Command
     * @throws RequiredOptionException
     */
    public function handle()
    {
        $this->provided('name');
        
        $class = $this->option('name');
        
        $filename = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . '.php';
        
        
        if (($connection = $this->option('connection'))) {
            $filename = $connection . DIRECTORY_SEPARATOR . $filename;
            $class = $class . strtoupper('__' . $connection);
            File::ensureDirectoryExists($this->migrationPath . DIRECTORY_SEPARATOR . $connection);
        }
        
        $filepath = $this->migrationPath . DIRECTORY_SEPARATOR . $filename;
        
        if (File::exists($filepath))
            throw new Exception("File [$filepath] already exists");
        
        if ($connection)
            File::put($filepath, str_replace([self::EXAMPLE_CONNECTION_CLASS, 'CONNECTION_VALUE'], [$class, $connection], File::get(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . self::EXAMPLE_CONNECTION_CLASS . '.php')));
        else File::put($filepath, str_replace(self::EXAMPLE_CLASS, $class, File::get(__DIR__ . DIRECTORY_SEPARATOR. ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . self::EXAMPLE_CLASS . '.php')));
    }
}
