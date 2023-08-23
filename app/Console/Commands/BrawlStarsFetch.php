<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\BrawlStarsRepository ;
use App\Services\BrawlStarsService ;

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

    $apitoken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjI0MmU0MDc1LTllYjctNDM3OS04Y2Y3LTM5YTRjNjlkMWI2NSIsImlhdCI6MTY5MjUzNjk0Niwic3ViIjoiZGV2ZWxvcGVyLzBmZDBlNDZkLTliZTEtZTQzMC1lODZjLTI2YWRjZDdmYzA5NyIsInNjb3BlcyI6WyJicmF3bHN0YXJzIl0sImxpbWl0cyI6W3sidGllciI6ImRldmVsb3Blci9zaWx2ZXIiLCJ0eXBlIjoidGhyb3R0bGluZyJ9LHsiY2lkcnMiOlsiNDEuMjI1LjM3LjIwMyJdLCJ0eXBlIjoiY2xpZW50In1dfQ.xjjXZoN0rECV00DdkoaRitetHtsNb1AZUFLXNhaXBS093Jen96F_OWPlx-ciLyclJexgHtRGDHcHD_xkJIQc3g';
    $tag ='#9CQV09G2U';

    $client = new BrawlStarsService($apitoken) ;
    $data = $client->getSampleData($tag, 50);

    $repo = new BrawlStarsRepository();
    $repo->loadToDataBase($data) ;



       
    }
}
