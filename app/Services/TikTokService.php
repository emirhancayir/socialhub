<?php

namespace App\Services;

use App\Models\PostMedia;
use App\Models\SocialAccount;

class TikTokService
{
    public function post(SocialAccount $account, string $title, ?PostMedia $media, int $postId = 0): array
    {
        if (!$media || $media->file_type !== 'video') {
            return ['success' => false, 'error' => 'TikTok yalnızca video destekler'];
        }

        $videoPath = storage_path('app/public/' . $media->file_path);

        if (!file_exists($videoPath)) {
            return ['success' => false, 'error' => 'Video dosyası bulunamadı'];
        }

        \Log::info('TikTokService called', [
            'video' => $videoPath,
            'title' => $title,
            'post_id' => $media->post_id,
        ]);

        // Caption'ı dosya adı olarak kullan ki tiktok_uploader açıklamayı ayarlayamasa bile
        // TikTok dosya adını caption olarak gösterir.
        $ext       = pathinfo($media->file_name, PATHINFO_EXTENSION) ?: 'mp4';
        $safeTitle = preg_replace('/\s+/', ' ', $title);
        $safeTitle = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $safeTitle);
        $safeTitle = trim(mb_substr($safeTitle, 0, 80)) ?: 'video';
        $tempDir   = sys_get_temp_dir();
        $tempPath  = $tempDir . DIRECTORY_SEPARATOR . $safeTitle . '.' . $ext;
        copy($videoPath, $tempPath);

        $scriptPath = base_path('scripts/tiktok_post.py');

        $command = 'py ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($tempPath)
            . ' ' . escapeshellarg($title)
            . ' ' . escapeshellarg((string) $postId)
            . ' 2>&1';

        $output = shell_exec($command);

        @unlink($tempPath);

        $lines    = array_filter(explode("\n", trim($output ?? '')));
        $lastLine = end($lines);
        $result   = json_decode($lastLine, true);

        if ($result && $result['success']) {
            return ['success' => true, 'post_id' => 'tiktok_' . uniqid()];
        }

        return ['success' => false, 'error' => $result['error'] ?? 'TikTok post başarısız'];
    }
}
