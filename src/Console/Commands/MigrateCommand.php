<?php
/**
 * This file contains MigrateCommand class
 */

namespace Sofiakb\Database\NoSQL\Console\Commands;


use Exception;
use Sofiakb\Database\NoSQL\Exceptions\RequiredOptionException;
use Sofiakb\Database\NoSQL\Migration;
use Sofiakb\Database\NoSQL\Schema\Attributes\Type;
use Sofiakb\Database\NoSQL\Schema\Attributes\Types;
use Sofiakb\Filesystem\Facades\File;

/**
 * Class MigrateCommand
 * Cette classe est chargé de récupérer le fichier
 * à traiter le passer au programme.
 *
 * @package Sofiakb\Database\NoSQL\Console\Commands
 * @author Sofiakb <contact.sofiak@gmail.com>
 */
class MigrateCommand extends Command
{
    private ?string $migrationPath;
    
    private const EXAMPLE_CLASS = 'ExampleMigration';
    
    /**
     * MigrateCommand constructor.
     * @param array|null $argv
     */
    public function __construct(array $argv = null)
    {
        parent::__construct($argv);
    }
    
    /**
     * Handle Command
     * @throws RequiredOptionException
     */
    public function handle()
    {
        $migrations = Migration::all();
        
        foreach ($migrations as $migration) {
            $this->warning("Migrating [" . get_class($migration) . "]...");
            $migration->up();
            $this->success("Migration [" . get_class($migration) . "] ended successfully");
        }
    }
}