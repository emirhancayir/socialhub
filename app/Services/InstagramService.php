<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;

class InstagramService
{
    public function post(SocialAccount $account, string $caption, ?PostMedia $media, int $postId = 0): array
    {
        if (!$media) {
            return ['success' => false, 'error' => 'Instagram en az bir medya dosyası gerektirir'];
        }

        $mediaPath = storage_path('app/public/' . $media->file_path);

        if (!file_exists($mediaPath)) {
            return ['success' => false, 'error' => 'Medya dosyası bulunamadı'];
        }

        $username   = $account->platform_username;
        $scriptPath = base_path('scripts/instagram_post.py');

        $command = 'py ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($username)
            . ' ' . escapeshellarg($mediaPath)
            . ' ' . escapeshellarg($caption)
            . ' ' . escapeshellarg((string) $postId)
            . ' 2>&1';

        $output = shell_exec($command);

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && ($result['success'] ?? false)) {
            return ['success' => true, 'post_id' => 'ig_' . ($result['media_id'] ?? uniqid())];
        }

        return ['success' => false, 'error' => $result['error'] ?? 'Instagram post başarısız'];
    }
}
