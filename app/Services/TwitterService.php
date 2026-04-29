<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;

class TwitterService
{
    private string $baseUrl = 'https://api.twitter.com/2';
    private string $uploadUrl = 'https://upload.twitter.com/1.1';

    public function post(SocialAccount $account, string $content, ?PostMedia $media): array
    {
        $token = $account->access_token;

        try {
            $payload = ['text' => $content];

            if ($media) {
                $mediaId = $this->uploadMedia($token, $media);
                if (!$mediaId) {
                    return ['success' => false, 'error' => 'Media upload failed'];
                }
                $payload['media'] = ['media_ids' => [$mediaId]];
            }

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/tweets", $payload);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json('data.id')];
            }

            return ['success' => false, 'error' => $response->json('detail', 'Tweet failed')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function uploadMedia(string $token, PostMedia $media): ?string
    {
        $filePath = storage_path('app/public/' . $media->file_path);

        $response = Http::withToken($token)
            ->attach('media', file_get_contents($filePath), $media->file_name)
            ->post("{$this->uploadUrl}/media/upload.json");

        if ($response->successful()) {
            return (string) $response->json('media_id_string');
        }

        return null;
    }
}
