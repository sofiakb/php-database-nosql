<?php
/**
 * Created by PhpStorm.
 * User: Sofiane Akbly <sofiane.akbly@gmail.com>
 * Date: 26/07/2021
 * Time: 11:55
 */

use Carbon\Carbon;

if (!function_exists('pluralize')) {
    /**
     * Pluralizes a word if quantity is not one.
     *
     * @param int $quantity Number of items
     * @param string $singular Singular form of word
     * @param string|null $plural Plural form of word; function will attempt to deduce plural form from singular if not provided
     * @return string Pluralized word if quantity is not one, otherwise singular
     */
    function pluralize(string $singular, int $quantity = 2, ?string $plural = null): string
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
}

if (!function_exists('toObject')) {
    /**
     * @param mixed $data
     * @return mixed|null
     */
    function toObject($data)
    {
        return $data ? json_decode(json_encode($data)) : null;
    }
}

if (!function_exists('toArray')) {
    /**
     * @param mixed $data
     * @return array|null
     */
    function toArray($data)
    {
        return $data ? json_decode(json_encode($data), true) : null;
    }
}

if (!function_exists('project_path')) {
    /**
     * Get the path to the project folder.
     *
     * @return string
     */
    function project_path(): string
    {
        list($scriptPath) = get_included_files();
        return dirname($scriptPath);
    }
}

if (!function_exists('today')) {
    /**
     * @param null $format
     * @return mixed
     */
    function today($format = null)
    {
        return $format ? Carbon::now()->format($format) : Carbon::now();
    }
}