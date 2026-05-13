<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;

class TwitterService
{
    public function post(SocialAccount $account, string $content, ?PostMedia $media, int $postId = 0): array
    {
        $mediaPath  = $media ? storage_path('app/public/' . $media->file_path) : '';
        $scriptPath = base_path('scripts/twitter_post.py');

        $command = 'py ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($content)
            . ' ' . escapeshellarg($mediaPath)
            . ' ' . escapeshellarg((string) $postId)
            . ' 2>&1';

        $output = shell_exec($command);

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            return ['success' => true, 'post_id' => $result['tweet_id'] ?? uniqid()];
        }

        return ['success' => false, 'error' => $result['error'] ?? 'Twitter post başarısız'];
    }
}
