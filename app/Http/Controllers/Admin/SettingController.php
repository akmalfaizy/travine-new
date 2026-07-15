<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'site_name', 'site_tagline', 'phone_whatsapp', 'email_contact',
            'address', 'facebook', 'instagram', 'whatsapp_api_url',
            'dp_percentage', 'bank_name', 'bank_account', 'bank_holder',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key));
        }

        ActivityLog::record('Memperbarui pengaturan website');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
