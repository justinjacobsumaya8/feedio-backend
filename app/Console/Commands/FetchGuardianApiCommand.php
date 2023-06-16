<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleBody;
use App\Models\Author;
use App\Models\Category;
use App\Models\DataSource;
use App\Models\Source;
use App\Services\GuardianApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchGuardianApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:guardian-api';

    const PAGE = 1;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get articles from theguardian.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->alert("Command Starting");

        $this->info("Pulling articles");
        $guardianApiService = new GuardianApiService();
        $articles = $guardianApiService->getArticles(self::PAGE);

        $bar = $this->output->createProgressBar(count($articles));
        $bar->start();

        foreach ($articles as $data) {
            $article = Article::where('web_url', $data['webUrl'])->first();
            if (!$article) {
                $article = new Article();
                $article->web_url = $data['webUrl'];
            }

            $source = Source::firstOrCreate([
                'name' => "The Guardian",
                'data_source_id' => DataSource::THE_GUARDIAN_ID
            ]);

            $category = Category::firstOrCreate([
                'name' => $data['sectionName'],
                'data_source_id' => DataSource::THE_GUARDIAN_ID
            ]);

            $article->title = $data['webTitle'];
            $article->source_id = $source->id;
            $article->category_id = $category->id;
            $article->thumbnail_url = $data['fields']['thumbnail'];
            $article->published_at = Carbon::parse($data['webPublicationDate']);
            $article->save();

            foreach ($data['blocks']['body'] as $body) {
                ArticleBody::firstOrCreate([
                    'article_id' => $article->id,
                    'content' => $body['bodyTextSummary'],
                ]);
            }

            foreach ($data['tags'] as $tag) {
                $author = Author::firstOrCreate([
                    'name' => $tag['webTitle'],
                    'data_source_id' => DataSource::THE_GUARDIAN_ID
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
