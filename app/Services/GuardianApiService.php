<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GuardianApiService
{
    public $apiDomain = "https://content.guardianapis.com";
    protected $apiKey = "8cd28f34-4bc1-4690-8be8-09f2bc474ca6";

    public function getArticles(int $page = 1): array
    {
        try {
            $request = Http::get("{$this->apiDomain}/search", [
                "page" => $page,
                "show-fields" => "starRating,headline,thumbnail",
                "show-tags" => "contributor",
                "show-blocks" => "body",
                "api-key" => $this->apiKey
            ]);

            return $request['response']['results'];
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
