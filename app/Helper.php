<?php

use App\Models\Setting;

function settings($key){
    $data = Setting::select('value')->where('key', $key)->first();
    return $data->value ?? '';
}
