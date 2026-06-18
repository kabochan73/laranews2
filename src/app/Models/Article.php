<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'topic',
        'zenn_id',
        'title',
        'slug',
        'emoji',
        'author_name',
        'author_username',
        'liked_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getUrlAttribute(): string
    {
        return "https://zenn.dev/{$this->author_username}/articles/{$this->slug}";
    }
}