<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;

class TikTokService
{
    private string $baseUrl = 'https://open.tiktokapis.com/v2';

    public function post(SocialAccount $account, string $title, ?PostMedia $media): array
    {
        $token = $account->access_token;

        try {
            if (!$media) {
                return ['success' => false, 'error' => 'TikTok requires a video file'];
            }

            if ($media->file_type !== 'video') {
                return ['success' => false, 'error' => 'TikTok only supports video uploads'];
            }

            return $this->postVideo($token, $title, $media);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function postVideo(string $token, string $title, PostMedia $media): array
    {
        $filePath = storage_path('app/public/' . $media->file_path);
        $fileSize = filesize($filePath);

        $initResponse = Http::withToken($token)
            ->post("{$this->baseUrl}/post/publish/video/init/", [
                'post_info' => [
                    'title'        => $title,
                    'privacy_level' => 'SELF_ONLY',
                ],
                'source_info' => [
                    'source'         => 'FILE_UPLOAD',
                    'video_size'     => $fileSize,
                    'chunk_size'     => $fileSize,
                    'total_chunk_count' => 1,
                ],
            ]);

        if (!$initResponse->successful()) {
            return ['success' => false, 'error' => $initResponse->json('error.message', 'Init failed')];
        }

        $uploadUrl = $initResponse->json('data.upload_url');
        $publishId = $initResponse->json('data.publish_id');

        $uploadResponse = Http::withHeaders([
            'Content-Range' => "bytes 0-" . ($fileSize - 1) . "/{$fileSize}",
            'Content-Type'  => $media->mime_type,
        ])->put($uploadUrl, file_get_contents($filePath));

        if ($uploadResponse->successful()) {
            return ['success' => true, 'post_id' => $publishId];
        }

        return ['success' => false, 'error' => 'Video upload failed'];
    }
}
