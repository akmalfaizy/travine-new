<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    /**
     * Show the form for editing social links.
     */
    public function edit()
    {
        $link = SocialLink::first();
        if (!$link) {
            $link = SocialLink::create([
                'instagram_url' => null,
                'tiktok_url'    => null,
                'youtube_url'   => null,
            ]);
        }
        return view('admin.social_links.edit', compact('link'));
    }

    /**
     * Update the social links.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'instagram_url' => 'nullable|url',
            'tiktok_url'    => 'nullable|url',
            'youtube_url'   => 'nullable|url',
        ]);

        $link = SocialLink::first();
        if ($link) {
            $link->update($data);
        } else {
            SocialLink::create($data);
        }

        return back()->with('success', 'Link media sosial berhasil diperbarui.');
    }
}
?>
