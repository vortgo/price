<?php

namespace App\Jobs\Parsers\Novus;

use App\Services\Parsers\DTO\Category;
use App\Services\Parsers\Novus\NovusParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $parser = new NovusParserService();

        $pageUrl = $parser->makeUrlToCategory($this->category->getLink());
        $lastPage = $parser->getLastPage($pageUrl);
        $delay = 5;
        \Log::info('test' . $lastPage);
        for ($i = 1; $i <= $lastPage; $i++) {
            $pageUrl = $parser->makeUrlToCategory($this->category->getLink(), $i);
            ParseListProducts::dispatch($pageUrl, $this->category)->delay($delay);
            $delay += 5;
        }
    }
}
