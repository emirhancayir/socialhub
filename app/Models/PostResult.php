<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostResult extends Model
{
    protected $fillable = [
        'post_id', 'platform', 'status', 'platform_post_id', 'error_message', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
