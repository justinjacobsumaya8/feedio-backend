<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleBody;
use App\Models\Author;
use App\Models\Category;
use App\Models\DataSource;
use App\Models\Source;
use App\Services\NewsApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchNewsApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news-api';

    /* 
        https://newsapi.org/docs/endpoints/top-headlines

        options = business, entertainment, general, health, science, sports, technology
    */
    const CATEGORY = "health";

    const PAGE = 1;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get articles from newsapi.org';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->alert("Command Starting");

        $this->info("Pulling articles");
        $newsApiService = new NewsApiService();
        $articles = $newsApiService->getArticles(self::PAGE, self::CATEGORY);

        $bar = $this->output->createProgressBar(count($articles));
        $bar->start();

        foreach ($articles as $data) {
            if (!$data['description']) {
                $bar->advance();
                continue;
            }

            $article = Article::where('web_url', $data['url'])->first();
            if (!$article) {
                $article = new Article();
                $article->web_url = $data['url'];
            }

            $source = Source::firstOrCreate([
                'name' => $data['source']['name'],
                'data_source_id' => DataSource::NEWS_API_ID
            ]);

            $category = Category::firstOrCreate([
                'name' => ucfirst(self::CATEGORY),
                'data_source_id' => DataSource::NEWS_API_ID
            ]);

            $article->title = $data['title'];
            $article->source_id = $source->id;
            $article->category_id = $category->id;
            $article->thumbnail_url = $data['urlToImage'];
            $article->published_at = Carbon::parse($data['publishedAt']);
            $article->save();

            ArticleBody::firstOrCreate([
                'article_id' => $article->id,
                'content' => $data['description'],
            ]);

            if ($data['author']) {
                $author = Author::firstOrCreate([
                    'name' => $data['author'],
                    'data_source_id' => DataSource::NEWS_API_ID
                ]);

                ArticleAuthor::firstOrCreate([
                    'article_id' => $article->id,
                    'author_id' => $author->id,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
