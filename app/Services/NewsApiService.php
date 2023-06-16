<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsApiService
{
    public $apiDomain = "https://newsapi.org";
    protected $apiKey = "1284962415fb4bcea7fcc2c3d2b9997c";

    public function getArticles(int $page = 1, string $category): array
    {
        try {
            $request = Http::get("{$this->apiDomain}/v2/top-headlines", [
                "page" => $page,
                "category" => $category,
                "country" => "us",
                "apiKey" => $this->apiKey
            ]);

            return $request['articles'];
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
