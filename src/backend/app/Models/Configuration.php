<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuration extends Model
{
    use SoftDeletes;

    protected $table = 'configuration';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public static function get($key)
    {
        $data = self::where('key', $key)->first();

        if (!$data) {
            return null;
        }

        return match ($data->type) {
            'boolean' => filter_var($data->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => intval($data->value),
            'string' => strval($data->value),
            'array' => json_decode($data->value, true),
            default => $data->value,
        };
    }

    public static function set($key, $value) : self
    {
        $type = match (gettype($value)) {
            'boolean' => 'boolean',
            'integer' => 'integer',
            'array' => 'array',
            default => 'string',
        };

        return self::updateOrCreate(
            ['key' => $key],
            ['key' => $key, 'value' => is_array($value) ? json_encode($value) : strval($value), 'type' => $type]
        );
    }

}
