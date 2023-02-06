<?php
/**
 * Created by PhpStorm.
 * User: Sofiane Akbly <sofiane.akbly@gmail.com>
 * Date: 26/07/2021
 * Time: 11:55
 */

namespace Sofiakb\Database\NoSQL\Tools;

use Carbon\Carbon;

class Helpers
{
    /**
     * Pluralizes a word if quantity is not one.
     *
     * @param int $quantity Number of items
     * @param string $singular Singular form of word
     * @param string|null $plural Plural form of word; static function will attempt to deduce plural form from singular if not provided
     * @return string Pluralized word if quantity is not one, otherwise singular
     */
    static function pluralize(string $singular, int $quantity = 2, ?string $plural = null): string
    {
        if ($quantity == 1 || !strlen($singular)) return $singular;
        if ($plural !== null) return $plural;
        
        $last_letter = strtolower($singular[strlen($singular) - 1]);
        switch ($last_letter) {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }
    
    
    /**
     * @param mixed $data
     * @return mixed|null
     */
    static function toObject($data)
    {
        return $data ? json_decode(json_encode($data)) : null;
    }
    
    
    /**
     * @param mixed $data
     * @return array|null
     */
    static function toArray($data): ?array
    {
        return $data ? json_decode(json_encode($data), true) : null;
    }
    
    
    /**
     * Get the path to the project folder.
     *
     * @return string
     */
    static function project_path(): string
    {
        list($scriptPath) = get_included_files();
        return dirname($scriptPath);
    }
    
    /**
     * @param null $format
     * @return Carbon|string
     */
    static function today($format = null)
    {
        return $format ? Carbon::now()->format($format) : Carbon::now();
    }
    
    
}