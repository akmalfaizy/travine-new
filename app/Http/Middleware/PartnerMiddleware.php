<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('partner')->check()) {
            return redirect()->route('partner.login')->with('error', 'Silakan login terlebih dahulu untuk mengakses Portal Partner.');
        }

        $partner = Auth::guard('partner')->user();

        if ($partner->status === 'suspended') {
            Auth::guard('partner')->logout();
            return redirect()->route('partner.login')->with('error', 'Akun Partner Anda sedang ditangguhkan. Silakan hubungi Customer Support.');
        }

        if ($partner->status === 'inactive') {
            Auth::guard('partner')->logout();
            return redirect()->route('partner.login')->with('error', 'Akun Partner Anda sedang nonaktif.');
        }

        return $next($request);
    }
}
