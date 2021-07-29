<?php


namespace Sofiakb\Database\NoSQL\Console\Commands;


use ReflectionClass;
use Sofiakb\Database\NoSQL\Exceptions\RequiredOptionException;
use Sofiakb\Filesystem\Facades\File;

/**
 * Class Command
 * @package Sofiakb\Database\NoSQL\Console\Commands
 */
class Command
{
    /**
     * @var array $options Liste des options
     */
    protected array $options;
    
    /**
     * @var array $parameters Tableau contenant les argv de PHP
     */
    protected array $parameters;
    
    /**
     * @var string|null $name
     */
    protected static ?string $name = null;
    
    /**
     * Command constructor.
     * @param array|null $argv
     */
    public function __construct(array $argv = null)
    {
        if ($argv) {
            $this->setParameters($argv);
        }
    }
    
    /**
     * Récupération des argv de PHP et
     * séparations en arguments & options.
     *
     * @param array $argv
     */
    public function setParameters(array $argv)
    {
        $this->parameters = array_slice($argv, 2);
        
        $this->setOptions($this->parameters);
    }
    
    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (count($options) > 0 && !is_string(array_keys($options)[0]))
            $options = array_combine(
                array_map(fn($item) => explode('=', $item, 2)[0], $options),
                array_map(fn($item) => explode('=', $item, 2)[1] ?? true, $options),
            );
        $this->options = $options;
    }
    
    public static function namespace(): string
    {
        return __NAMESPACE__;
    }
    
    /**
     * @return array
     */
    public static function getCommands()
    {
        $reflector = new ReflectionClass(self::class);
        $dir = File::dirname($reflector->getFileName());
        $files = array_filter(File::files($dir), fn($file) => $file->getName() !== 'Command.php');
        
        $commands = [];
        foreach ($files as $file) {
            $class = '\\' . self::namespace() . '\\' . File::name($file->getName());
            if (class_exists($class)) {
                if (isset($class::$name) && !is_null($class::$name))
                    $name = $class::$name;
                else
                    $name = strtolower(preg_replace('/(?<!^)[A-Z]/', ':$0', str_replace('Command', '', File::name($file->getName()))));
                $commands[$name] = $class;
            }
        }
        return $commands;
    }
    
    /**
     * @param string $key
     * @return mixed|null
     */
    protected function option(string $key)
    {
        return $this->options["--$key"] ?? null;
    }
    
    /**
     * @param mixed ...$keys
     * @return bool
     * @throws RequiredOptionException
     */
    protected function provided(...$keys): bool
    {
        if (is_string($keys))
            $keys = [$keys];
        
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if ($this->option($key) === null || trim($this->option($key)) === '')
                    throw new RequiredOptionException($key, $this->getName());
            }
        }
        return true;
    }
    
    protected function getName()
    {
        $class = File::name((new ReflectionClass(get_called_class()))->getFileName());
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ':$0', str_replace('Command', '', $class)));
    }
    
    protected function success($message)
    {
        echo "\033[32m$message\033[0m" . PHP_EOL;
    }
    
    protected function warning($message)
    {
        echo "\033[33m$message\033[0m" . PHP_EOL;
    }
    
    /**
     * Permet de renseigner une chaîne de caractère
     * dans un champ caché.
     *
     * @param string|null $question
     * @return string
     */
    public function secret(string $question = null): string
    {
        if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0) {
            $exe = __DIR__ . '/Resources/bin/hiddeninput.exe';
            
            $sExec = shell_exec($exe);
            return rtrim($sExec);
        } else {
            echo sprintf("\033[32m%s\033[0m%s", $question, PHP_EOL);
            shell_exec('stty -echo');
            $value = trim(fgets(STDIN));
            shell_exec('stty echo');
            return $value;
        }
        
        
        /*if ('\\' === \DIRECTORY_SEPARATOR) {
            $exe = __DIR__.'/../Resources/bin/hiddeninput.exe';

            // handle code running from a phar
            if ('phar:' === substr(__FILE__, 0, 5)) {
                $tmpExe = sys_get_temp_dir().'/hiddeninput.exe';
                copy($exe, $tmpExe);
                $exe = $tmpExe;
            }

            $sExec = shell_exec($exe);
            $value = $trimmable ? rtrim($sExec) : $sExec;
            $output->writeln('');

            if (isset($tmpExe)) {
                unlink($tmpExe);
            }

            return $value;
        }*/
    }
}