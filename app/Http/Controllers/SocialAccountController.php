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
        $driver = $this->driverMap[$platform] ?? $platform;
        return Socialite::driver($driver)->redirect();
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

    public function destroy(SocialAccount $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);
        $account->delete();
        return back()->with('success', 'Hesap bağlantısı kaldırıldı.');
    }
}
