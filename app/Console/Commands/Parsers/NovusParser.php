<?php

namespace App\Console\Commands\Parsers;

use App\Jobs\Parsers\Novus\ParseCategories;
use App\Jobs\Parsers\Novus\ParseCategory;
use App\Services\Parsers\Novus\NovusParserService;
use Illuminate\Console\Command;

class NovusParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:novus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse novus';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $parser = new NovusParserService();
        $categories = $parser->parseCategories();
        ParseCategory::dispatch($categories[0]);
    }
}
