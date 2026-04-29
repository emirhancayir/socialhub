<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;

class InstagramService
{
    private string $baseUrl = 'https://graph.instagram.com';

    public function post(SocialAccount $account, string $caption, ?PostMedia $media): array
    {
        $token = $account->access_token;
        $userId = $account->platform_user_id;

        try {
            if ($media && $media->file_type === 'image') {
                return $this->postImage($token, $userId, $caption, $media);
            } elseif ($media && $media->file_type === 'video') {
                return $this->postVideo($token, $userId, $caption, $media);
            } else {
                return ['success' => false, 'error' => 'Instagram requires at least one media file'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function postImage(string $token, string $userId, string $caption, PostMedia $media): array
    {
        $containerResponse = Http::post("{$this->baseUrl}/v18.0/{$userId}/media", [
            'image_url'   => $media->url,
            'caption'     => $caption,
            'access_token' => $token,
        ]);

        if (!$containerResponse->successful()) {
            return ['success' => false, 'error' => $containerResponse->json('error.message', 'Container creation failed')];
        }

        $containerId = $containerResponse->json('id');

        $publishResponse = Http::post("{$this->baseUrl}/v18.0/{$userId}/media_publish", [
            'creation_id'  => $containerId,
            'access_token' => $token,
        ]);

        if ($publishResponse->successful()) {
            return ['success' => true, 'post_id' => $publishResponse->json('id')];
        }

        return ['success' => false, 'error' => $publishResponse->json('error.message', 'Publish failed')];
    }

    private function postVideo(string $token, string $userId, string $caption, PostMedia $media): array
    {
        $containerResponse = Http::post("{$this->baseUrl}/v18.0/{$userId}/media", [
            'media_type'   => 'REELS',
            'video_url'    => $media->url,
            'caption'      => $caption,
            'access_token' => $token,
        ]);

        if (!$containerResponse->successful()) {
            return ['success' => false, 'error' => $containerResponse->json('error.message', 'Container creation failed')];
        }

        $containerId = $containerResponse->json('id');

        // Video upload is async - poll for status
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(5);
            $statusResponse = Http::get("{$this->baseUrl}/v18.0/{$containerId}", [
                'fields'       => 'status_code',
                'access_token' => $token,
            ]);

            if ($statusResponse->json('status_code') === 'FINISHED') {
                break;
            }
        }

        $publishResponse = Http::post("{$this->baseUrl}/v18.0/{$userId}/media_publish", [
            'creation_id'  => $containerId,
            'access_token' => $token,
        ]);

        if ($publishResponse->successful()) {
            return ['success' => true, 'post_id' => $publishResponse->json('id')];
        }

        return ['success' => false, 'error' => $publishResponse->json('error.message', 'Publish failed')];
    }
}
