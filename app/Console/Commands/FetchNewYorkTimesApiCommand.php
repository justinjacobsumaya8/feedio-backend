<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleBody;
use App\Models\Author;
use App\Models\Category;
use App\Models\DataSource;
use App\Models\Source;
use App\Services\NewYorkTimesApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchNewYorkTimesApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:ny-times-api';

    /*
        News Desk Values - https://developer.nytimes.com/docs/articlesearch-product/1/overview

        Adventure Sports, Arts, Business, Jobs, etc.
    */
    const NEWS_DESK_VALUE = "Arts";

    const PAGE = 1;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get articles from nytimes.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->alert("Command Starting");

        $this->info("Pulling articles");
        $nyTimesApiService = new NewYorkTimesApiService();
        $articles = $nyTimesApiService->getArticles(self::PAGE, self::NEWS_DESK_VALUE);

        $bar = $this->output->createProgressBar(count($articles));
        $bar->start();

        foreach ($articles as $data) {
            if (!$data['source'] || !$data['byline']['original']) {
                $bar->advance();
                continue;
            }

            $article = Article::where('web_url', $data['web_url'])->first();
            if (!$article) {
                $article = new Article();
                $article->web_url = $data['web_url'];
            }

            $source = Source::firstOrCreate([
                'name' => $data['source'],
                'data_source_id' => DataSource::THE_NEW_YORK_TIMES_ID
            ]);

            $category = Category::firstOrCreate([
                'name' => $data['section_name'],
                'data_source_id' => DataSource::THE_NEW_YORK_TIMES_ID
            ]);

            $article->title = $data['headline']['main'];
            $article->source_id = $source->id;
            $article->category_id = $category->id;
            $article->thumbnail_url = isset($data['multimedia'][0]) ? "https://static01.nyt.com/" . $data['multimedia'][0]['url'] : null;
            $article->published_at = Carbon::parse($data['pub_date']);
            $article->save();

            ArticleBody::firstOrCreate([
                'article_id' => $article->id,
                'content' => $data['lead_paragraph'],
            ]);

            $author = Author::firstOrCreate([
                'name' => preg_replace('/^By /', '', $data['byline']['original']),
                'data_source_id' => DataSource::THE_NEW_YORK_TIMES_ID
            ]);

            ArticleAuthor::firstOrCreate([
                'article_id' => $article->id,
                'author_id' => $author->id,
            ]);

            $bar->advance();
        }
        $bar->finish();
    }
}
