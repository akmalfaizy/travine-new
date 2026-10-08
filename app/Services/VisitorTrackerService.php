<?php

namespace App\Services;

use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorTrackerService
{
    /**
     * Record a visit from HTTP request
     */
    public static function record(Request $request): ?VisitorLog
    {
        // Don't track if method is not GET or HEAD
        if (!in_array($request->method(), ['GET', 'HEAD'])) {
            return null;
        }

        $path = '/' . ltrim($request->path(), '/');

        // Ignore admin, livewire, api, asset or internal routes
        if (
            str_starts_with($path, '/admin') ||
            str_starts_with($path, '/up') ||
            str_starts_with($path, '/_debugbar') ||
            str_starts_with($path, '/livewire') ||
            str_starts_with($path, '/storage') ||
            preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|webp|ico|woff|woff2|ttf|map)$/i', $path)
        ) {
            return null;
        }

        $ip = $request->ip() ?: '127.0.0.1';
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $userAgent = $request->userAgent() ?: '';

        // Prevent spam / multiple consecutive hits on same path within 3 seconds for same session
        $throttleKey = 'vtrack_' . md5($ip . '_' . ($sessionId ?: 'guest') . '_' . $path);
        if (Cache::has($throttleKey)) {
            return null;
        }
        Cache::put($throttleKey, 1, now()->addSeconds(3));

        $deviceInfo = self::parseUserAgent($userAgent);
        $geoInfo    = self::resolveLocation($ip, $request);

        return VisitorLog::create([
            'ip_address'   => $ip,
            'session_id'   => $sessionId,
            'path'         => $path,
            'url'          => $request->fullUrl(),
            'method'       => $request->method(),
            'referer'      => $request->header('referer'),
            'user_agent'   => substr($userAgent, 0, 1000),
            'device_type'  => $deviceInfo['device_type'],
            'browser'      => $deviceInfo['browser'],
            'platform'     => $deviceInfo['platform'],
            'is_bot'       => $deviceInfo['is_bot'],
            'country'      => $geoInfo['country'] ?? 'Indonesia',
            'country_code' => $geoInfo['country_code'] ?? 'ID',
            'province'     => $geoInfo['province'] ?? null,
            'city'         => $geoInfo['city'] ?? null,
            'postal_code'  => $geoInfo['postal_code'] ?? null,
            'visited_at'   => now(),
        ]);
    }

    /**
     * Resolve IP Geolocation with caching & Cloudflare fallback
     */
    public static function resolveLocation(string $ip, Request $request): array
    {
        // 1. Check Cloudflare / CDN headers first
        if ($cfCity = $request->header('CF-IPCity')) {
            return [
                'city'         => $cfCity,
                'province'     => $request->header('CF-Region') ?: 'Indonesia',
                'country'      => $request->header('CF-IPCountryName') ?: 'Indonesia',
                'country_code' => $request->header('CF-IPCountry') ?: 'ID',
                'postal_code'  => $request->header('CF-Postal-Code'),
            ];
        }

        // 2. Check if local / private IP
        $isLocal = in_array($ip, ['127.0.0.1', '::1']) ||
                   str_starts_with($ip, '192.168.') ||
                   str_starts_with($ip, '10.') ||
                   str_starts_with($ip, '172.16.');

        if ($isLocal) {
            return [
                'city'         => 'Lokal (Development)',
                'province'     => 'Localhost',
                'country'      => 'Indonesia',
                'country_code' => 'ID',
                'postal_code'  => null,
            ];
        }

        // 3. Cache public IP lookups for 30 days
        $cacheKey = 'geoip_' . md5($ip);
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($ip) {
            try {
                // Free GeoIP lookup (ip-api.com) with 1.2s timeout
                $response = Http::timeout(1.2)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city,zip");
                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'city'         => $data['city'] ?? 'Unknown',
                            'province'     => $data['regionName'] ?? 'Unknown',
                            'country'      => $data['country'] ?? 'Indonesia',
                            'country_code' => $data['countryCode'] ?? 'ID',
                            'postal_code'  => $data['zip'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Ignore API failure silently to keep user response instant
            }

            return [
                'city'         => 'Indonesia',
                'province'     => 'Indonesia',
                'country'      => 'Indonesia',
                'country_code' => 'ID',
                'postal_code'  => null,
            ];
        });
    }

    /**
     * Parse User Agent into Device Type, Browser, Platform
     */
    public static function parseUserAgent(string $ua): array
    {
        $uaLower = strtolower($ua);

        // 1. Bot detection
        $isBot = false;
        $botPatterns = ['bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'whatsapp', 'telegrambot', 'twitterbot'];
        foreach ($botPatterns as $bot) {
            if (str_contains($uaLower, $bot)) {
                $isBot = true;
                break;
            }
        }

        // 2. Device Type
        $deviceType = 'desktop';
        if ($isBot) {
            $deviceType = 'bot';
        } elseif (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua)) {
            $deviceType = 'tablet';
        } elseif (preg_match('/(mobi|ipod|iphone|android|blackberry|opera mini|iemobile|wpdesktop)/i', $ua)) {
            $deviceType = 'mobile';
        }

        // 3. Platform / OS
        $platform = 'Unknown';
        if (str_contains($uaLower, 'windows')) {
            $platform = 'Windows';
        } elseif (str_contains($uaLower, 'android')) {
            $platform = 'Android';
        } elseif (str_contains($uaLower, 'iphone') || str_contains($uaLower, 'ipad') || str_contains($uaLower, 'ipod')) {
            $platform = 'iOS';
        } elseif (str_contains($uaLower, 'mac os') || str_contains($uaLower, 'macintosh')) {
            $platform = 'macOS';
        } elseif (str_contains($uaLower, 'linux')) {
            $platform = 'Linux';
        }

        // 4. Browser
        $browser = 'Other';
        if (str_contains($uaLower, 'edg') || str_contains($uaLower, 'edge')) {
            $browser = 'Edge';
        } elseif (str_contains($uaLower, 'opr') || str_contains($uaLower, 'opera')) {
            $browser = 'Opera';
        } elseif (str_contains($uaLower, 'chrome') && !str_contains($uaLower, 'chromium')) {
            $browser = 'Chrome';
        } elseif (str_contains($uaLower, 'safari') && !str_contains($uaLower, 'chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($uaLower, 'firefox')) {
            $browser = 'Firefox';
        }

        return [
            'device_type' => $deviceType,
            'browser'     => $browser,
            'platform'    => $platform,
            'is_bot'      => $isBot,
        ];
    }
}
