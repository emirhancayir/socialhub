<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id', 'platform', 'platform_user_id', 'platform_username',
        'platform_name', 'avatar', 'access_token', 'refresh_token', 'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPlatformLabelAttribute(): string
    {
        return match($this->platform) {
            'instagram' => 'Instagram',
            'twitter'   => 'X (Twitter)',
            'tiktok'    => 'TikTok',
            'facebook'  => 'Facebook',
            default     => ucfirst($this->platform),
        };
    }
}
