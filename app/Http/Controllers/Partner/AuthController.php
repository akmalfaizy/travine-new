<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Mail\PartnerOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Show registration form
    public function showRegister()
    {
        return view('partner.auth.register');
    }

    // Handle registration request
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $partner = Partner::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'referral_code' => Partner::generateReferralCode($data['name']),
            'status' => 'active', // active by default
        ]);

        // Auto login after registration
        Auth::guard('partner')->login($partner);
        return redirect()->route('partner.dashboard');
    }

    // Show login form
    public function showLogin()
    {
        return view('partner.auth.login');
    }

    // Handle login request (email or username)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $partner = Partner::where('email', $credentials['login'])->first();

        if (!$partner || !Hash::check($credentials['password'], $partner->password)) {
            return back()->withErrors(['login' => 'Email atau password yang Anda masukkan salah.'])->withInput($request->only('login'));
        }

        if ($partner->status !== 'active') {
            $msg = $partner->status === 'suspended'
                ? 'Akun Partner Anda sedang ditangguhkan. Silakan hubungi admin.'
                : 'Akun Partner Anda berstatus nonaktif.';
            return back()->withErrors(['login' => $msg]);
        }

        // Generate 6-digit OTP code
        $otpCode = (string) rand(100000, 999999);
        $partner->otp_code = $otpCode;
        $partner->otp_expires_at = Carbon::now()->addMinutes(10);
        $partner->otp_attempts = 0;
        $partner->otp_sent_at = Carbon::now();
        $partner->save();

        // Store pending partner ID & remember flag in session without authenticating yet
        session([
            'partner_2fa_id'       => $partner->id,
            'partner_2fa_remember' => $request->boolean('remember'),
        ]);

        try {
            Mail::to($partner->email)->send(new PartnerOtpMail($partner, $otpCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email OTP partner #' . $partner->id . ': ' . $e->getMessage());
        }

        return redirect()->route('partner.otp')->with('success', 'Kode OTP 6-digit telah dikirimkan ke email Anda.');
    }

    // Show OTP verification form
    public function showOtp()
    {
        $partnerId = session('partner_2fa_id');
        if (!$partnerId) {
            return redirect()->route('partner.login');
        }

        $partner = Partner::find($partnerId);
        if (!$partner) {
            return redirect()->route('partner.login');
        }

        return view('partner.auth.otp', compact('partner'));
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $partnerId = session('partner_2fa_id');
        if (!$partnerId) {
            return redirect()->route('partner.login')->withErrors(['login' => 'Sesi verifikasi habis. Silakan login kembali.']);
        }

        $partner = Partner::find($partnerId);
        if (!$partner) {
            return redirect()->route('partner.login');
        }

        if (!$partner->otp_expires_at || $partner->otp_expires_at->lt(Carbon::now())) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan minta kode baru.']);
        }

        if ($partner->otp_attempts >= 5) {
            return back()->withErrors(['otp' => 'Batas percobaan OTP terlampaui. Silakan login kembali.']);
        }

        if ((string) $partner->otp_code === (string) $request->input('otp')) {
            $partner->otp_code = null;
            $partner->otp_expires_at = null;
            $partner->otp_attempts = 0;
            $partner->save();

            $remember = session('partner_2fa_remember', false);
            session()->forget(['partner_2fa_id', 'partner_2fa_remember']);

            // Authenticate securely now that 2FA OTP is verified
            Auth::guard('partner')->login($partner, $remember);
            $request->session()->regenerate();

            return redirect()->route('partner.dashboard')->with('success', 'Login berhasil. Selamat datang kembali, ' . $partner->name);
        }

        $partner->increment('otp_attempts');
        $sisa = max(0, 5 - $partner->otp_attempts);
        return back()->withErrors(['otp' => "Kode OTP salah. Sisa kesempatan: {$sisa} kali."]);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $partnerId = session('partner_2fa_id');
        if (!$partnerId) {
            return redirect()->route('partner.login');
        }

        $partner = Partner::find($partnerId);
        if (!$partner) {
            return redirect()->route('partner.login');
        }

        $otpCode = (string) rand(100000, 999999);
        $partner->otp_code = $otpCode;
        $partner->otp_expires_at = Carbon::now()->addMinutes(10);
        $partner->otp_attempts = 0;
        $partner->otp_sent_at = Carbon::now();
        $partner->save();

        try {
            Mail::to($partner->email)->send(new PartnerOtpMail($partner, $otpCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim ulang OTP partner #' . $partner->id . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
    }

    // Show password reset request form
    public function showForgotPassword()
    {
        return view('partner.auth.forgot_password');
    }

    // Send password reset token (reuse existing password reset table)
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $partner = Partner::where('email', $request->email)->first();
        if (!$partner) {
            return back()->withErrors(['email' => 'Email not found']);
        }
        // Use Laravel's Password broker for partners (configured in auth.php)
        $status = \Illuminate\Support\Facades\Password::broker('partners')->sendResetLink(
            $request->only('email')
        );
        return $status == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Show password reset form (token provided via query)
    public function showResetForm(Request $request, $token = null)
    {
        return view('partner.auth.reset_password', ['token' => $token, 'email' => $request->email]);
    }

    // Handle password reset submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = \Illuminate\Support\Facades\Password::broker('partners')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($partner, $password) {
                $partner->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status == \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('partner.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Logout
    public function logout()
    {
        Auth::guard('partner')->logout();
        return redirect()->route('partner.login');
    }
}
?>
