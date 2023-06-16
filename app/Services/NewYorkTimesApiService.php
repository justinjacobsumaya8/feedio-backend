<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewYorkTimesApiService
{
    public $apiDomain = "https://api.nytimes.com";
    protected $apiKey = "PGlsKdA3oCfTOVDRjuM6Ixexz9bPxviM";

    public function getArticles(int $page = 1, string $newsDesk): array
    {
        try {
            $request = Http::get("{$this->apiDomain}/svc/search/v2/articlesearch.json", [
                "fq" => 'news_desk:("' . $newsDesk . '")',
                "page" => $page,
                "begin_date" => "20220101",
                "end_date" => "20231231",
                "api-key" => $this->apiKey
            ]);

            return $request['response']['docs'];
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
