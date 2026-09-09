<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Mail\PartnerOtpMail;
use App\Mail\PartnerResetPasswordMail;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PartnerAuthController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::guard('partner')->check()) {
            return redirect()->route('partner.dashboard');
        }
        return view('partner.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:partners,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $referralCode = Partner::generateReferralCode($request->input('name'));

        $partner = Partner::create([
            'name'           => $request->input('name'),
            'email'          => strtolower(trim($request->input('email'))),
            'phone'          => $request->input('phone'),
            'password'       => $request->input('password'),
            'referral_code'  => $referralCode,
            'status'         => 'pending',
            'otp_code'       => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts'   => 0,
            'otp_sent_at'    => now(),
        ]);

        // Send OTP email
        try {
            Mail::to($partner->email)->send(new PartnerOtpMail($partner, $otpCode));
        } catch (\Throwable $e) {
            Log::error('Partner OTP Email send failed: ' . $e->getMessage());
        }

        session(['pending_partner_id' => $partner->id]);

        return redirect()->route('partner.verifyOtpPage')
            ->with('success', 'Pendaftaran berhasil! Kode verifikasi 6 digit telah dikirimkan ke email ' . $partner->email);
    }

    public function showVerifyOtpForm()
    {
        $partnerId = session('pending_partner_id');
        $partner = $partnerId ? Partner::find($partnerId) : null;

        if (Auth::guard('partner')->check()) {
            return redirect()->route('partner.dashboard');
        }

        return view('partner.auth.verify-otp', compact('partner'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $partnerId = session('pending_partner_id');
        $partner = $partnerId ? Partner::find($partnerId) : null;

        if (!$partner) {
            $partner = Partner::where('email', $request->input('email'))->where('status', 'pending')->first();
        }

        if (!$partner) {
            return redirect()->route('partner.login')->with('error', 'Sesi verifikasi habis. Silakan login atau mendaftar kembali.');
        }

        if ($partner->otp_attempts >= 5) {
            return back()->withErrors(['otp_code' => 'Batas percobaan OTP telah terlampaui. Silakan klik "Kirim Ulang OTP".']);
        }

        if (!$partner->otp_expires_at || now()->greaterThan($partner->otp_expires_at)) {
            $partner->increment('otp_attempts');
            return back()->withErrors(['otp_code' => 'Kode OTP telah kadaluarsa. Silakan minta kode OTP baru.']);
        }

        if ($partner->otp_code !== trim($request->input('otp_code'))) {
            $partner->increment('otp_attempts');
            return back()->withErrors(['otp_code' => 'Kode OTP yang Anda masukkan tidak sesuai.']);
        }

        // Verify success
        $partner->update([
            'email_verified_at' => now(),
            'status'            => 'active',
            'otp_code'          => null,
            'otp_expires_at'    => null,
            'otp_attempts'      => 0,
        ]);

        session()->forget('pending_partner_id');
        Auth::guard('partner')->login($partner);

        return redirect()->route('partner.dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat datang di Portal Mitra Travine.id');
    }

    public function resendOtp(Request $request)
    {
        $partnerId = session('pending_partner_id');
        $partner = $partnerId ? Partner::find($partnerId) : null;

        if (!$partner && $request->input('email')) {
            $partner = Partner::where('email', $request->input('email'))->where('status', 'pending')->first();
        }

        if (!$partner) {
            return redirect()->route('partner.login')->with('error', 'Sesi tidak ditemukan.');
        }

        // Cooldown check (60 seconds)
        if ($partner->otp_sent_at && now()->diffInSeconds($partner->otp_sent_at) < 60) {
            $remaining = 60 - now()->diffInSeconds($partner->otp_sent_at);
            return back()->with('error', "Harap tunggu {$remaining} detik sebelum meminta OTP baru.");
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $partner->update([
            'otp_code'       => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts'   => 0,
            'otp_sent_at'    => now(),
        ]);

        try {
            Mail::to($partner->email)->send(new PartnerOtpMail($partner, $otpCode));
        } catch (\Throwable $e) {
            Log::error('Partner Resend OTP Email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Kode OTP baru telah dikirimkan ke email Anda.');
    }

    public function showLoginForm()
    {
        if (Auth::guard('partner')->check()) {
            return redirect()->route('partner.dashboard');
        }
        return view('partner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        $partner = Partner::where('email', strtolower(trim($credentials['email'])))->first();

        if (!$partner || !Hash::check($credentials['password'], $partner->password)) {
            return back()->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])->withInput($request->only('email'));
        }

        if ($partner->status === 'pending') {
            session(['pending_partner_id' => $partner->id]);
            return redirect()->route('partner.verifyOtpPage')->with('warning', 'Akun Anda belum diverifikasi. Silakan masukkan kode OTP yang telah dikirim ke email Anda.');
        }

        if ($partner->status === 'suspended') {
            return back()->withErrors(['email' => 'Akun Mitra Anda sedang ditangguhkan. Silakan hubungi customer service.']);
        }

        if ($partner->status === 'inactive') {
            return back()->withErrors(['email' => 'Akun Mitra Anda nonaktif.']);
        }

        Auth::guard('partner')->login($partner, $remember);
        $request->session()->regenerate();

        return redirect()->route('partner.dashboard')->with('success', 'Berhasil login sebagai Mitra!');
    }

    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login')->with('success', 'Anda telah keluar dari akun Mitra.');
    }

    public function showForgotPasswordForm()
    {
        return view('partner.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->input('email')));

        $partner = Partner::where('email', $email)->first();

        // Always show friendly message to prevent email enumeration
        if ($partner) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'email'      => $email,
                    'token'      => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            try {
                Mail::to($partner->email)->send(new PartnerResetPasswordMail($partner, $token));
            } catch (\Throwable $e) {
                Log::error('Partner reset password email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Jika alamat email tersebut terdaftar di sistem kami, instruksi reset password telah dikirimkan.');
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        $email = $request->query('email');
        return view('partner.auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->input('email')));
        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$tokenRecord || !Hash::check($request->input('token'), $tokenRecord->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau telah kadaluarsa.']);
        }

        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Token reset password telah kadaluarsa. Silakan ajukan ulang.']);
        }

        $partner = Partner::where('email', $email)->first();
        if ($partner) {
            $partner->password = $request->input('password');
            $partner->save();
            DB::table('password_reset_tokens')->where('email', $email)->delete();
        }

        return redirect()->route('partner.login')->with('success', 'Password Anda berhasil diperbarui. Silakan login kembali.');
    }
}
