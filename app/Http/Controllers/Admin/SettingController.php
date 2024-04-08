<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        $keys = $request->except('_token');
        // if ($request->has('site_logo') && $request->hasFile('site_logo')) {
        //    if(!empty(config('settings.site_logo'))){
        //        deleteImage(config('settings.site_logo'));
        //    }
        //      $keys['site_logo'] =saveImage($request->site_logo, 142, 24, 'storage/logo');
        // }
        // if ($request->has('site_favicon') && $request->hasFile('site_favicon')) {
        //     if(!empty(config('settings.site_favicon'))){
        //         deleteImage(config('settings.site_favicon'));
        //     }
        //     $keys['site_favicon'] = saveImage($request->site_favicon, 50, 50, 'storage/favicon');
        // }
        foreach ($keys as $key => $value) {
            Setting::set($key, $value);
        }
        return redirect()->back()->with('success', 'Setting Updated Successfully');
    }
}
