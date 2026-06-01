<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Récupérer un paramètre global
     */
    public static function get($key, $default = null)
    {
        if (!DB::getSchemaBuilder()->hasTable('global_settings')) {
            return $default;
        }

        $cacheKey = "global_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = DB::table('global_settings')
                ->where('key', $key)
                ->first();
            
            if (!$setting) {
                return $default;
            }

            // Convertir selon le type
            switch ($setting->type) {
                case 'integer':
                    return (int) $setting->value;
                case 'decimal':
                    return (float) $setting->value;
                case 'boolean':
                    return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
                case 'json':
                    return json_decode($setting->value, true);
                default:
                    return $setting->value ?? $default;
            }
        });
    }

    /**
     * Récupérer plusieurs paramètres à la fois
     */
    public static function getMany(array $keys)
    {
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = self::get($key);
        }
        return $settings;
    }

    /**
     * Mettre à jour un paramètre
     */
    public static function set($key, $value, $type = 'string')
    {
        if (!DB::getSchemaBuilder()->hasTable('global_settings')) {
            return false;
        }

        DB::table('global_settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'updated_at' => now()
            ]
        );

        // Vider le cache
        Cache::forget("global_setting_{$key}");
        
        return true;
    }

    /**
     * Vider le cache des paramètres
     */
    public static function clearCache()
    {
        Cache::flush();
    }
}




