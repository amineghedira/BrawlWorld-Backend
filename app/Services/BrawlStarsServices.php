<?php

namespace App\Services ;

require 'App/Services/BrawlStarsClient.php' ;
use App\Services\BrawlStarsClient ;


class BrawlStarsServices {



    // Properties 
    private $client ;
    // Constructor
    public function __construct($apitoken) {
           $this->client = new BrawlStarsClient($apitoken);
    }

    // Methods
    public function getBattleLog($tag) {

        $battleLog = $this->client->getPlayerBattleLog($tag);
        return $battleLog ;
    }

    public function checkBattleDate($battle) {

        $battleTime = $battle['battleTime'];
        $battleDate = substr($battleTime,0,8);
        $currentDate = gmdate('Ymd');
    
        if ($battleDate===$currentDate)
         return true;

        else
         return false;
    }

    public function checkBattle($battle) {

        if (!$this->checkBattleDate($battle))
          return false;

        $type = $battle['battle']['type'] ;
        $mode = $battle['battle']['mode'];
        $map = $battle['event']['map'] ;

        if ($type ==='ranked') {

          if( strpos($mode,'Showdown') || $map === null )
            return false;

          else
            return true;  
        }
        
        return false; 
    }

    public function isFirstTeam($players, $tag) {

        for ($i=0 ; $i<3 ; $i++){

            $player = $players[$i];
    
            if ($player['tag'] === '#'.$tag)
              
              return true ;
        }
         return false;

    }

    public function battleData($battle, $players, $isFirstTeam){

        $map = $battle['event']['map'];
        $result = $battle['battle']['result'];

        $data = [
            'map' => $map,
            'result' => $result
        ] ;

        $brawlers = array_map(fn($player) => $player['brawler']['name'], $players) ;

        $keys = array_map( fn($i) => 'brawler'.$i, range(1, 6));
        
        if (($isFirstTeam && ($result === 'defeat') ) || ($isFirstTeam && ($result === 'victory'))) {
          
         $brawlers = array_combine($keys, array_reverse($brawlers)); 

         return array_merge($data, $brawlers);
        }
        
        else {

         $brawlers = array_combine($keys, $brawlers);

         return array_merge($data, $brawlers);
        }
        
    }

    public function getBattleData($battle, $tag) {

     $players = array_merge(...$battle['battle']['teams']);

     $isFirstTeam = $this->isFirstTeam($players, $tag);
    
     return $this->battleData($battle, $players, $isFirstTeam) ;

    }
    

    public function checkBattleLog($tag) {
     
     $data =[];
     $battleLog = $this->getBattleLog($tag);
     $battle = $battleLog[0];

     //check whether the latest Battle is of today's date and a ranked mode.
     if ($this->checkBattle($battle)) {

        $data['tag'] = $tag ;
        $data['battle1'] = $this->getBattleData($battle, $tag) ;

        for($i=1; $i<25; $i++){

          $battle = $battleLog[$i];

          if($this->checkBattle($battle))

            $data['battle'.($i+1)] = $this->getBattleData($battle, $tag);

          else
          
            return $data;                
        }

        return $data;
        }
 
     return false;
    }

 




}