<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    public static function get($key)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->first();
        if (!$entry) {
            return;
        }
        return $entry->value;
    }
    public static function set($key, $value = null)
    {
        $setting = new self();
        $entry = $setting->where('key', $key)->first();
        if ($entry != null) {
            $entry->key = $key;
            $entry->value = $value;
            $entry->saveOrFail();
        } else {
            $entry = $setting->create([
                'key' => $key,
                'value' => $value,
            ]);
        }
        // Config::set('key', $value);
        // if (Config::get($key) == $value) {
        //     return true;
        // }
        return ;
    }
}
