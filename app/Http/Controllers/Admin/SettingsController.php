<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;

class SettingsController extends Controller
{
    
    public function index()
    {
        $settings = Settings::all();
        return response()->json($settings);
    }

    public function update(Request $request, $key)
    {
        $settings = Settings::where('key', $key)->first();
        //$settings->key = $request->key;
        $settings->value = $request->value;
        $settings->is_active = $request->is_active;
        $settings->save();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting updated successfully',
            'data' => $settings,
            'status' => 'success'
        ], 201);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,gif,webp|max:2048'
        ]);

        $file = $request->file('logo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $path = $file->storeAs('company-logos', $filename, 'public');
        
        if (!$path) {
            return response()->json([
                'message' => 'Failed to upload logo',
                'status' => 'error'
            ], 500);
        }

        // Update or create logo setting
       $settings = Settings::updateOrCreate(
            ['key' => 'logo'],
            ['value' => $filename, 'is_active' => true]
        );

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'filename' => $filename,
            'data' => $settings,
            'url' => Storage::url($path),
            'status' => 'success'
        ]);
    }


    public function deleteLogo($filename)
    {
        // Delete the file
        $deleted = Storage::disk('public')->delete('settings/logos/' . $filename);
        
        if (!$deleted) {
            return response()->json([
                'message' => 'Logo file not found',
                'status' => 'error'
            ], 404);
        }

        // Remove the setting
        Settings::where('key', 'logo')->delete();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Logo deleted successfully',
            'status' => 'success'
        ]);
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $setting = Settings::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $request->value,
                'is_active' => $request->input('is_active', true)
            ]
        );

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting created successfully',
            'data' => $setting,
            'status' => 'success'
        ], 201);
    }

  
    public function destroy($key)
    {
        $setting = Settings::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found',
                'status' => 'error'
            ], 404);
        }

        // If it's a logo, delete the file too
        if ($key === 'logo' && $setting->value) {
            Storage::disk('public')->delete('settings/logos/' . $setting->value);
        }

        $setting->delete();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting deleted successfully',
            'status' => 'success'
        ]);
    }

   
    public function toggle($key)
    {
        $setting = Settings::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found',
                'status' => 'error'
            ], 404);
        }

        $setting->is_active = !$setting->is_active;
        $setting->save();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting toggled successfully',
            'data' => $setting,
            'status' => 'success'
        ]);
    }

  
}