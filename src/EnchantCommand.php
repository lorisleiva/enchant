<?php

namespace Lorisleiva\Enchant;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\ClientException;
use Lorisleiva\LaravelIntelligence\IntelligenceCenter;

class EnchantCommand extends Command
{
    protected $signature = 'enchant {version?}';
    protected $description = 'Enchant a new documentation';

    public function handle(IntelligenceCenter $intelligenceCenter)
    {
        $http = new Client(['base_uri' => config('enchant.url')]);
        $book = config('enchant.book');
        $key = config('enchant.key');
        $version = $this->argument('version');

        if (! $book || ! $key) {
            $this->error('Please provide the following environemnt variables: ENCHANT_BOOK and ENCHANT_KEY.');
            exit(1);
        }

        $this->line("➤ Creating a new chapter...");

        $knowledge = $intelligenceCenter->learn()->export(true);

        try {
            $response = $http->post("/api/$book/chapters", [
                'json' => compact('key', 'version', 'knowledge'),
            ]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                $this->error("Book \"{$book}\" with key \"{$key}\" not found.");
                exit(1);
            }
            throw $e;
        }

        $chapter = json_decode($response->getBody()->getContents());
        $versionUrl = $chapter->book->url . ($version ? "/$version" : '');

        $this->info("✔ A new chapter was created for \"{$book}\"");
        $this->line("  <comment>=> Version URL:</comment> {$versionUrl}");
        $this->line("  <comment>=> Chapter URL:</comment> {$chapter->url}");
    }
}
