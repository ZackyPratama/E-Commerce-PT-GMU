<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    //fillable attributes for mass assignment
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    // scope to only settings of a certain group
    #[Scope]
    protected function group(Builder $query, string $group) : void
    {
        $query->where('group', $group);
    }

    // Helper method

    // helper method to get a setting value by key with optional default value
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if(!$setting) {
            return $default;
        }
        return static::castValue($setting->value, $setting->type);
        
    }

    // helper method to set a setting value by key and type
    public static function set(string $key, $value, $type = 'string', $group = 'general') : void
    {
        $setting = static::updateOrCreate( 
            ['key' => $key], 
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }

    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
        };
    }

}
