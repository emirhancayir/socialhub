<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostResult;
use App\Models\SocialAccount;
use App\Services\InstagramService;
use App\Services\TikTokService;
use App\Services\TwitterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishPost implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        $this->post->update(['status' => 'publishing']);

        $media = $this->post->media->first();

        foreach ($this->post->platforms as $platform) {
            $account = SocialAccount::where('user_id', $this->post->user_id)
                ->where('platform', $platform)
                ->first();

            if (!$account) {
                PostResult::create([
                    'post_id'       => $this->post->id,
                    'platform'      => $platform,
                    'status'        => 'failed',
                    'error_message' => 'Bu platform için bağlı hesap bulunamadı.',
                ]);
                continue;
            }

            // TikTok, Instagram ve Twitter her zaman gerçek Python scripti kullanır
            $result = in_array($platform, ['tiktok', 'instagram', 'twitter'])
                ? $this->realPost($platform, $account, $media)
                : (env('DEMO_MODE', false)
                    ? $this->demoPost($platform)
                    : $this->realPost($platform, $account, $media));

            PostResult::create([
                'post_id'          => $this->post->id,
                'platform'         => $platform,
                'status'           => $result['success'] ? 'success' : 'failed',
                'platform_post_id' => $result['post_id'] ?? null,
                'error_message'    => $result['error'] ?? null,
                'published_at'     => $result['success'] ? now() : null,
            ]);
        }

        $results  = PostResult::where('post_id', $this->post->id)->get();
        $anySuccess = $results->some(fn($r) => $r->status === 'success');

        $this->post->update([
            'status' => $anySuccess ? 'published' : 'failed',
        ]);
    }

    private function realPost(string $platform, SocialAccount $account, $media): array
    {
        $title   = trim((string) $this->post->title);
        $content = trim((string) $this->post->content);

        // TikTok için title + content birleşimi (newline yok, shell ile uyumlu)
        $tiktokCaption = trim($title . ($title && $content ? ' — ' : '') . $content) ?: 'SocialHub';

        $twitterCaption = trim($title . ($title && $content ? ' — ' : '') . $content) ?: 'SocialHub';

        $postId = $this->post->id;

        return match($platform) {
            'instagram' => (new InstagramService)->post($account, $twitterCaption, $media, $postId),
            'twitter'   => (new TwitterService)->post($account, $twitterCaption, $media, $postId),
            'tiktok'    => (new TikTokService)->post($account, $tiktokCaption, $media, $postId),
            default     => ['success' => false, 'error' => 'Desteklenmeyen platform'],
        };
    }

    private function demoPost(string $platform): array
    {
        // Demo modda 1 saniyelik bekleme ile başarılı simülasyon
        sleep(1);
        return [
            'success' => true,
            'post_id' => 'demo_' . $platform . '_' . uniqid(),
        ];
    }
}
