<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFolderSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sourceArticles()
    {
        return $this->hasMany(Article::class, 'source_id', 'source_id');
    }

    public function categoryArticles()
    {
        return $this->hasMany(Article::class, 'category_id', 'category_id');
    }

    public function authorArticles()
    {
        return $this->hasMany(ArticleAuthor::class, 'author_id', 'author_id');
    }

    public function userFeeds()
    {
        return $this->hasMany(UserFeed::class);
    }
}
