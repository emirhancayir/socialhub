<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAccountController extends Controller
{
    private array $supportedPlatforms = ['instagram', 'twitter', 'tiktok'];

    private array $driverMap = [
        'twitter' => 'twitter-oauth-2',
    ];

    public function redirect(string $platform)
    {
        abort_if(!in_array($platform, $this->supportedPlatforms), 404);

        if ($platform === 'tiktok') {
            return $this->tiktokLogin();
        }

        if ($platform === 'instagram') {
            return $this->instagramCookieLogin();
        }

        if ($platform === 'twitter') {
            return $this->twitterCookieLogin();
        }

        $driver = $this->driverMap[$platform] ?? $platform;
        return Socialite::driver($driver)->redirect();
    }

    public function instagramCookieLogin()
    {
        @set_time_limit(0);

        $scriptPath = base_path('scripts/instagram_login.py');
        $output = shell_exec('py ' . escapeshellarg($scriptPath) . ' 2>&1');

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            $username = $result['username'];
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'instagram',
                    'platform_user_id' => $username,
                ],
                [
                    'platform_username' => $username,
                    'platform_name'     => $result['full_name'] ?? $username,
                    'avatar'            => null,
                    'access_token'      => 'instagrapi_session',
                    'refresh_token'     => null,
                    'token_expires_at'  => now()->addMonths(3),
                ]
            );

            return redirect()->route('dashboard')->with('success', 'Instagram hesabı bağlandı: @' . $username);
        }

        return redirect()->route('dashboard')->with('error', 'Instagram bağlantısı başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'));
    }

    public function tiktokLogin()
    {
        @set_time_limit(0);

        $scriptPath = base_path('scripts/tiktok_login.py');
        $output = shell_exec('py ' . escapeshellarg($scriptPath) . ' 2>&1');

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            $username = $result['username'] ?? 'tiktok_user';
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'tiktok',
                    'platform_user_id' => $username,
                ],
                [
                    'platform_username' => $username,
                    'platform_name'     => $username,
                    'avatar'            => null,
                    'access_token'      => 'cookie_based',
                    'refresh_token'     => null,
                    'token_expires_at'  => now()->addMonths(6),
                ]
            );

            return redirect()->route('dashboard')->with('success', 'TikTok hesabı bağlandı!');
        }

        return redirect()->route('dashboard')->with('error', 'TikTok bağlantısı başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'));
    }

    public function callback(string $platform)
    {
        abort_if(!in_array($platform, $this->supportedPlatforms), 404);

        try {
            $driver     = $this->driverMap[$platform] ?? $platform;
            $socialUser = Socialite::driver($driver)->user();

            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => $platform,
                    'platform_user_id' => $socialUser->getId(),
                ],
                [
                    'platform_username' => $socialUser->getNickname() ?? $socialUser->getName(),
                    'platform_name'     => $socialUser->getName(),
                    'avatar'            => $socialUser->getAvatar(),
                    'access_token'      => $socialUser->token,
                    'refresh_token'     => $socialUser->refreshToken,
                    'token_expires_at'  => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                ]
            );

            return redirect()->route('dashboard')->with('success', ucfirst($platform) . ' hesabı başarıyla bağlandı!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Hesap bağlanırken hata oluştu: ' . $e->getMessage());
        }
    }

    public function demoStore(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:instagram,twitter,tiktok',
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:4',
        ]);

        $platform = $request->platform;
        $username = ltrim($request->username, '@');

        SocialAccount::updateOrCreate(
            [
                'user_id'          => auth()->id(),
                'platform'         => $platform,
                'platform_user_id' => 'demo_' . $platform . '_' . auth()->id(),
            ],
            [
                'platform_username' => $username,
                'platform_name'     => $username,
                'avatar'            => null,
                'access_token'      => 'demo_token_' . $platform,
                'refresh_token'     => null,
                'token_expires_at'  => now()->addYear(),
            ]
        );

        return redirect()->route('dashboard')->with('success', ucfirst($platform) . ' hesabı bağlandı! (@' . $username . ')');
    }

    public function twitterCookieLogin()
    {
        @set_time_limit(0);

        $scriptPath = base_path('scripts/twitter_login.py');
        $output = shell_exec('py ' . escapeshellarg($scriptPath) . ' 2>&1');

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            $username = $result['username'];
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'twitter',
                    'platform_user_id' => $username,
                ],
                [
                    'platform_username' => $username,
                    'platform_name'     => $result['full_name'] ?? $username,
                    'avatar'            => null,
                    'access_token'      => 'twikit_session',
                    'refresh_token'     => null,
                    'token_expires_at'  => now()->addMonths(3),
                ]
            );

            return redirect()->route('dashboard')->with('success', 'Twitter/X hesabı bağlandı: @' . $username);
        }

        return redirect()->route('dashboard')->with('error', 'Twitter bağlantısı başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'));
    }

    private function instagramLogin(string $username, string $password)
    {
        @set_time_limit(0);

        $scriptPath = base_path('scripts/instagram_login.py');
        $command = 'py ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($username)
            . ' ' . escapeshellarg($password)
            . ' 2>&1';

        $output = shell_exec($command);

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            $username = $result['username'];
            SocialAccount::updateOrCreate(
                [
                    'user_id'          => auth()->id(),
                    'platform'         => 'instagram',
                    'platform_user_id' => $username,
                ],
                [
                    'platform_username' => $username,
                    'platform_name'     => $result['full_name'] ?? $username,
                    'avatar'            => null,
                    'access_token'      => 'instagrapi_session',
                    'refresh_token'     => null,
                    'token_expires_at'  => now()->addMonths(3),
                ]
            );

            return redirect()->route('dashboard')->with('success', 'Instagram hesabı bağlandı: @' . $username);
        }

        return redirect()->route('dashboard')->with('error', 'Instagram bağlantısı başarısız: ' . ($result['error'] ?? 'Bilinmeyen hata'));
    }

    public function destroy(SocialAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);
        $account->delete();
        return back()->with('success', 'Hesap bağlantısı kaldırıldı.');
    }
}
