<?php

namespace App\Http\Controllers;

use App\Mail\ContactReplyMail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = Contact::create($validated);

        // Kirim email balasan otomatis
        try {
            Mail::to($contact->email)->send(new ContactReplyMail($contact));
        } catch (\Throwable $e) {
            Log::error('Contact reply mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim! Tim kami akan segera menghubungi Anda.',
        ]);
    }
}
