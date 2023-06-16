<?php

namespace Database\Seeders;

use App\Models\DataSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->dataSources() as $dataSource) {
            DataSource::firstOrCreate([
                "name" => $dataSource->name,
                "api_domain" => $dataSource->api_domain,
            ]);
        }
    }

    protected function dataSources(): array
    {
        return [
            (object) [
                "name" => "The Guardian",
                "api_domain" => "https://content.guardianapis.com"
            ],
            (object) [
                "name" => "NewsAPI",
                "api_domain" => "https://newsapi.org"
            ],
            (object) [
                "name" => "The New York Times",
                "api_domain" => "https://api.nytimes.com"
            ],
        ];
    }
}
