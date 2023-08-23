<?php

namespace App\Repositories ;

use App\Models\Stat ;
use App\Models\Map ;
use App\Models\Mode ;
use App\Models\Brawler ;


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

    public function addBrawlerToStat($brawler_id) {

        $map_ids = Map::pluck('id')->toArray();
        $rows = [];

        foreach ($map_ids as $map_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'map_id' => $map_id
            ];
            array_push($rows, $row );
        }

        Stat::insert($rows) ;
    }

    public function addMapToStat($map_id) {

        $brawlers_ids = Brawler::pluck('id')->toArray();
        $rows = [];

        foreach ($brawlers_ids as $brawler_id) {
            $row = [
                'brawler_id' => $brawler_id,
                'map_id' => $map_id
            ];
            array_push($rows, $row );
        }

        Stat::insert($rows) ;
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
            }
            
            $mode_id = $mode->id ;   
            $this->addMap($map_name, $mode_id);
            $map_id = Map::where('name', $map_name)->first()->id ;
            $this-> addMapToStat($map_id);
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
               $this-> addBrawlerToStat($brawler_id);
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

            Stat::where('map_id', $map_id)
            ->where('brawler_id', $brawler_id)
            ->increment('number_of_picks');
        }
    }

    public function addWins($battleData) {

        $map_id = $battleData[0] ;

        for($i=1 ; $i<4 ; $i++) {

            $brawler_id = $battleData[$i];
           
            Stat::where('map_id', $map_id)
            ->where('brawler_id', $brawler_id)
            ->increment('number_of_wins');
        }
    }

    public function loadToStat($battle) {

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

                  $this->loadToStat($battle);
        }


    }


}