<?php

namespace App\Console\Commands\Parsers;

use App\Services\Parsers\Novus\Category;
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
        $category = new Category();
        $category->setLink('/ru/babies/')->setName('Детское');

        $parser = new NovusParserService();
        $parser->updateProductsPrices($category);
    }
}
