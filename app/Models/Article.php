<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['published_at_formatted'];

    public function articleBodies()
    {
        return $this->hasMany(ArticleBody::class);
    }

    public function articleAuthors()
    {
        return $this->hasMany(ArticleAuthor::class);
    }

    public function getPublishedAtFormattedAttribute()
    {
        return Carbon::parse($this->published_at)->diffForHumans();
    }

    public function userFeeds()
    {
        return $this->hasMany(UserFeed::class);
    }

    public function scopeGeneralQuery($query)
    {
        return $query->select(
            'articles.*',
            'sources.name AS source_name',
            'categories.name AS category_name',
        )
            ->leftJoin('sources', 'sources.id', 'articles.source_id')
            ->leftJoin('categories', 'categories.id', 'articles.category_id')
            ->with(['articleAuthors' => function ($q) {
                $q->select(
                    'article_authors.*',
                    'authors.name AS author_name'
                )
                    ->leftJoin('authors', 'authors.id', 'article_authors.author_id');
            }])
            ->with('articleBodies');
    }
}
