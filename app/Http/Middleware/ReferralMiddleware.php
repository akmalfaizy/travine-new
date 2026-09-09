<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use App\Models\PartnerClick;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ReferralMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $refCode = $request->query('ref') ?? $request->query('referral');

        if ($refCode) {
            $codeUpper = strtoupper(trim($refCode));
            $partner = Partner::where('referral_code', $codeUpper)
                ->whereIn('status', ['active', 'pending'])
                ->first();

            if ($partner) {
                // Save to session and cookie for 30 days
                session(['travine_referral_code' => $partner->referral_code]);
                Cookie::queue('travine_referral_code', $partner->referral_code, 60 * 24 * 30);

                // Record click log
                try {
                    PartnerClick::create([
                        'partner_id'    => $partner->id,
                        'referral_code' => $partner->referral_code,
                        'landing_url'   => $request->fullUrl(),
                        'ip_address'    => $request->ip(),
                        'user_agent'    => $request->userAgent(),
                    ]);
                } catch (\Throwable $e) {
                    // Ignore tracking errors so user experience is uninterrupted
                }
            }
        }

        return $next($request);
    }
}
