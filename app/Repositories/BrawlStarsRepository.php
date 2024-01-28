<?php

namespace App\Repositories ;

use App\Models\Brawler ;
use App\Models\Mode ;
use App\Models\Map ;
use App\Models\BrawlerMap ;
use App\Models\BrawlerMode ;


class BrawlStarsRepository {
    
    //properties
    static $updateDate ;

    //constructor
    public function __construct() {
        
        /*if ($updateDate !== null) {
    
            $currentDate = gmdate('ymd');

            if($currentDate >= $updateDate)
             
                $this->DeleteOldStats() ; }*/

    }

    //methods
    public function addMode($mode_name) {

        $mode = new Mode;
        $mode->name = $mode_name;
        $mode->save();
    }

    public function addMap($map_name, $mode_id) {
        $map = new Map;
        $map->mode_id = $mode_id ;
        $map->name = $map_name;
        $map->save();
       
    }

    public function addBrawler($brawler_name) {
        $brawler = new Brawler;
        $brawler->name = $brawler_name;
        $brawler->save();
       
    }

    public function addBrawlerToMaps($brawler_id) {

        $map_ids = Map::pluck('id')->toArray();
        $rows = [];

        foreach ($map_ids as $map_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'map_id' => $map_id
            ];
            array_push($rows, $row );
        }

        BrawlerMap::insert($rows) ;
    }

    public function addBrawlerToModes($brawler_id) {

        $mode_ids = Mode::pluck('id')->toArray();
        $rows = [];

        foreach ($mode_ids as $mode_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'mode_id' => $mode_id
            ];
            array_push($rows, $row );
        }

        BrawlerMode::insert($rows) ;
    }

    public function addMapToBrawlers($map_id) {

        $brawlers_ids = Brawler::pluck('id')->toArray();
        $rows = [];

        foreach ($brawlers_ids as $brawler_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'map_id' => $map_id
            ];
            array_push($rows, $row );
        }

        BrawlerMap::insert($rows) ;
    }
    public function addModeToBrawlers($mode_id) {

        $brawlers_ids = Brawler::pluck('id')->toArray();
        $rows = [];

        foreach ($brawlers_ids as $brawler_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'mode_id' => $mode_id
            ];
            array_push($rows, $row );
        }

        BrawlerMode::insert($rows) ;
    }

    public function processBattleData($battle){

        $battleData =[] ;
        $map_name = $battle['map'] ;
        $map = Map::where('name', $map_name)->first() ;

        if ($map === null) {

            $mode_name = $battle['mode'] ;
            $mode = Mode::where('name', $mode_name)->first() ;

            if($mode === null) {

               $this->addMode($mode_name) ;
               $mode = Mode::where('name', $mode_name)->first() ;
               $mode_id = $mode->id ;  
               $this-> addModeToBrawlers($mode_id);
            }

            else
               $mode_id = $mode->id ;  

            $this->addMap($map_name, $mode_id);
            $map_id = Map::where('name', $map_name)->first()->id ;
            $this-> addMapToBrawlers($map_id);
        }
        else 
            $map_id = $map->id ;
          
        array_push($battleData, $map_id );

        $brawlers = array_filter($battle, function($key) {
            return strpos($key, 'brawler') === 0;
        }, ARRAY_FILTER_USE_KEY);

        foreach($brawlers as $brawler_name) {

            $brawler = Brawler::where('name', $brawler_name)->first() ;

            if ($brawler === null) {

               $this->addBrawler($brawler_name);
               $brawler_id = Brawler::where('name', $brawler_name)->first()->id;
               $this->addBrawlerToMaps($brawler_id);
               $this->addBrawlerToModes($brawler_id);
            } 

            else
                $brawler_id = $brawler->id ;

           array_push($battleData, $brawler_id);
        }
        return $battleData ;
    }

    public function addPicks($battleData) {
        
        $map_id = $battleData[0] ;

        for($i=1 ; $i<7 ; $i++) {

            $brawler_id = $battleData[$i];

            BrawlerMap::where('map_id', $map_id)
            ->where('brawler_id', $brawler_id)
            ->increment('number_of_picks');
        }
    }

    public function addWins($battleData) {

        $map_id = $battleData[0] ;

        for($i=1 ; $i<4 ; $i++) {

            $brawler_id = $battleData[$i];
           
            BrawlerMap::where('map_id', $map_id)
            ->where('brawler_id', $brawler_id)
            ->increment('number_of_wins');
        }
    }

    public function loadBattle($battle) {

        $battleData = $this->processBattleData($battle) ;
        $result = $battle['result'];
        
        if ($result === 'draw')

            $this->addPicks($battleData) ;

        else {

            $this->addPicks($battleData) ;
            $this->addWins($battleData) ; 
        }
    }       

    

    public function loadToDataBase($data) {

        foreach($data as $battle) {

                  $this->loadBattle($battle);
        }


    }

    public function rank($rows, $rankWinRate){

        $rows = $rows->sortByDesc('pick_rate');
           $counter=1;
        foreach ($rows as $row){

            $row->pick_rate_rank = $counter ;
            $row->save();
            $counter++;
        }
        
        if ($rankWinRate){
            
            $rows = $rows->sortByDesc('win_rate');
            $counter=1;

            foreach ($rows as $index => $row) {
                $row->win_rate_rank = $counter ;
                $row->save(); 
                $counter++;       
            }
        }

      
    }
    public function brawlerMapStats() {
        $map_ids = Map::pluck('id')->toArray();
        foreach($map_ids as $map_id) {

          $rows = BrawlerMap::where('map_id', $map_id)->get();

          $total_picks = $rows->sum('number_of_picks');

          foreach ($rows as $row) {
  
            $picks = $row->number_of_picks;
            $wins = $row->number_of_wins;
            $row->pick_rate = $picks/$total_picks;
            if($picks===0)
                $row->win_rate = 0 ;
            else
                $row->win_rate = $wins/$picks;
            $row->save();
            }

          $this->rank($rows,true);

        }
    }

    public function brawlerModeStats() {
        $mode_ids = Mode::pluck('id')->toArray();

        foreach($mode_ids as $mode_id) {

            $map_ids = Map::where('mode_id', $mode_id)->pluck('id')->toArray();

            $brawlerMapRows = BrawlerMap::whereIn('map_id', $map_ids)->get();
  
            $total_picks = $brawlerMapRows->sum('number_of_picks');
            
            $brawlerModeRows = BrawlerMode::where('mode_id', $mode_id)->get();
            foreach($brawlerModeRows as $brawlerModeRow) {
                $brawler_id = $brawlerModeRow->brawler_id ;
                $rows = $brawlerMapRows->filter(fn($item) => $item->brawler_id === $brawler_id);
                $picks = $rows->sum('number_of_picks');
                $wins = $rows->sum('number_of_wins');
                $brawlerModeRow->pick_rate = $picks/$total_picks;
                if($picks===0)
                   $brawlerModeRow->win_rate = 0 ;
                else
                   $brawlerModeRow->win_rate = $wins/$picks;

                $brawlerModeRow->save();
            }

            $this->rank($brawlerModeRows,true);
        }

    }

    public function generalStats() {

        $total_picks = BrawlerMap::sum('number_of_picks');
        $brawlerRows = Brawler::where([])->get();
        $modeRows = Mode::all();
        $mapRows =  Map::all();
        foreach($brawlerRows as $brawlerRow){
    
            $rows = BrawlerMap::where('brawler_id', $brawlerRow->id)->get();
            $picks = $rows->sum('number_of_picks');
            $wins = $rows->sum('number_of_wins');
            $brawlerRow->pick_rate = $picks/$total_picks;
            $brawlerRow->win_rate = $wins/$picks;
            $brawlerRow->save();
        }

        $this->rank($brawlerRows,true);

        foreach($mapRows as $mapRow){
            $rows = BrawlerMap::where('map_id', $mapRow->id)->get();
            $picks = $rows->sum('number_of_picks');
            $mapRow->pick_rate = $picks/$total_picks;
            $mapRow->save();

        }

        $this->rank($mapRows,false);

        foreach($modeRows as $modeRow){

            $map_ids = Map::where('mode_id', $modeRow->id)->pluck('id')->toArray();
            $rows = BrawlerMap::whereIn('map_id', $map_ids)->get();
            $picks = $rows->sum('number_of_picks');
            $modeRow->pick_rate = $picks/$total_picks;
            $modeRow->save();
    
        }
        $this->rank($modeRows,false);

    }

    public function calculateStats() {
        $this->brawlerMapStats();
        $this->brawlerModeStats();
        $this->generalStats();

    }

}