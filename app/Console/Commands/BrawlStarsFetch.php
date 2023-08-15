<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BrawlStarsFetch extends Command
{
    protected $signature = 'brawlstars:fetch';
    protected $description = 'Fetch, process and store Brawl Stars data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       
    }
}
