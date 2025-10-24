<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public $timestamps = false;

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getJson(string $key, $default = [])
    {
        $value = static::get($key);
        return $value ? json_decode($value, true) : $default;
    }

    public static function setJson(string $key, $value): void
    {
        static::set($key, json_encode($value));
    }
}
