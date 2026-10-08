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
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'email'; // we only have email field for partners
        $attempt = Auth::guard('partner')->attempt([
            $field => $credentials['login'],
            'password' => $credentials['password'],
            'status' => 'active',
        ]);

        if ($attempt) {
            // Generate OTP for extra verification
            $partner = Auth::guard('partner')->user();
            $partner->otp_code = rand(100000, 999999);
            $partner->otp_expires_at = Carbon::now()->addMinutes(10);
            $partner->otp_attempts = 0;
            $partner->save();

            Mail::to($partner->email)->send(new PartnerOtpMail($partner, (string) $partner->otp_code));

            return redirect()->route('partner.otp');
        }

        return back()->withErrors(['login' => 'Invalid credentials or inactive account']);
    }

    // Show OTP verification form
    public function showOtp()
    {
        return view('partner.auth.otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $partner = Auth::guard('partner')->user();
        if (!$partner) {
            return redirect()->route('partner.login');
        }

        if ($partner->otp_expires_at->lt(Carbon::now())) {
            return back()->withErrors(['otp' => 'OTP expired']);
        }

        if ($partner->otp_attempts >= 3) {
            return back()->withErrors(['otp' => 'Maximum attempts exceeded']);
        }

        if ($partner->otp_code == $request->input('otp')) {
            $partner->otp_code = null;
            $partner->otp_expires_at = null;
            $partner->otp_attempts = null;
            $partner->save();
            return redirect()->route('partner.dashboard');
        }

        $partner->increment('otp_attempts');
        return back()->withErrors(['otp' => 'Invalid OTP']);
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
