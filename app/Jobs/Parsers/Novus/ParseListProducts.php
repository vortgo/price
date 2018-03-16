<?php

namespace App\Jobs\Parsers\Novus;

use App\Services\Parsers\DTO\Category;
use App\Services\Parsers\Novus\NovusParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ParseListProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;
    private $category;

    public function __construct(string $url, Category $category)
    {
        $this->url = $url;
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
        $parser->updateProductsPriceOnPage($this->url,$this->category);
    }
}
