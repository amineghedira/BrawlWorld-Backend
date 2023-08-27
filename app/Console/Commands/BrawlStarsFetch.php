<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\BrawlStarsRepository ;
use App\Services\BrawlStarsService ;
use Illuminate\Support\Facades\Log;

class BrawlStarsFetch extends Command
{
    protected $signature = 'your:fetchBS ';
    protected $description = 'Fetch, process and store Brawl Stars data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

    $apitoken = env('API_TOKEN') ;
    $tag = env('ORIGINAL_TAG') ;
    $sampleSize = (int) env('SAMPLE_SIZE') ;

    Log::info('fetchBS command started running');

    $client = new BrawlStarsService($apitoken) ;
    $data = $client->getSampleData($tag, $sampleSize);

    $repo = new BrawlStarsRepository();
    $repo->loadToDataBase($data) ;
    $repo = new BrawlStarsRepository();
    $repo->calculateStats();

    Log::info('fetchBS command run successfully!');
       
    }
}
