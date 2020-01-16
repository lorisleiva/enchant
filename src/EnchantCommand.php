<?php

namespace Lorisleiva\Enchant;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Lorisleiva\LaravelIntelligence\IntelligenceCenter;

class EnchantCommand extends Command
{
    protected $signature = 'enchant {version?}';
    protected $description = 'Enchant a new documentation';

    public function handle(IntelligenceCenter $intelligenceCenter)
    {
        $book = config('enchant.book');
        $key = config('enchant.key');
        $version = $this->argument('version');

        if (! $book || ! $key) {
            $this->error('Please provide the following environemnt variables: ENCHANT_BOOK and ENCHANT_KEY.');
            exit(1);
        }

        $http = new Client([
            'base_uri' => config('enchant.url'),
            'timeout'  => 10.0,
        ]);

        $this->line("➤ Creating a new chapter...");

        $knowledge = $intelligenceCenter->learn()->export(true);

        $response = $http->post("/api/$book/chapters", [
            'json' => compact('version', 'knowledge'),
        ]);

        $chapter = json_decode($response->getBody()->getContents(), true);
        $versionUrl = $chapter['book']['url'] . ($version ? "/$version" : '');
        $chapterUrl = $chapter['url'];

        $this->info("✔ A new chapter was created for \"{$book}\"");
        $this->line("  <comment>=> Version URL:</comment> {$versionUrl}");
        $this->line("  <comment>=> Chapter URL:</comment> {$chapterUrl}");
    }
}
