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
        
        if (($connection = $this->option('connection')))
            $filename = strtoupper($connection . '--') . $filename;
        
        
        if (File::exists($filename))
            throw new Exception("File [$filename] already exists");
        
        File::put($this->migrationPath . DIRECTORY_SEPARATOR . str_replace('--', '', $filename), str_replace(self::EXAMPLE_CLASS, $class, File::get(project_path() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . self::EXAMPLE_CLASS . '.php')));
    }
}