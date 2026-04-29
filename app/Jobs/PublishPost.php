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

            $result = env('DEMO_MODE', false)
                ? $this->demoPost($platform)
                : $this->realPost($platform, $account, $media);

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
        return match($platform) {
            'instagram' => (new InstagramService)->post($account, $this->post->content ?? '', $media),
            'twitter'   => (new TwitterService)->post($account, $this->post->content ?? '', $media),
            'tiktok'    => (new TikTokService)->post($account, $this->post->title ?? $this->post->content ?? '', $media),
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
