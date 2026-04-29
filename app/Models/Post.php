<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'content', 'title', 'platforms', 'status', 'scheduled_at',
    ];

    protected $casts = [
        'platforms'    => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }

    public function results()
    {
        return $this->hasMany(PostResult::class);
    }
}
