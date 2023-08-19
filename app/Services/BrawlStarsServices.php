<?php

namespace App\Services ;

require 'App/Services/BrawlStarsClient.php' ;
use App\Services\BrawlStarsClient ;


class BrawlStarsServices {

    // Properties 
    private $lastTimeChecked;
    private $client ;

    // Constructor
    public function __construct($apitoken) {
           $this->client = new BrawlStarsClient($apitoken);
           $this->lastTimeChecked = $this->lastTimeChecked();
    }

    // Methods
    function lastTimeChecked() {

     $format = 'Ymd\THis\.000\Z';
     $timestamp = time() - (2 * 3600) - (55 * 60); // Current timestamp minus 2 hours and 55 minutes

     return gmdate($format, $timestamp);
    }

    public function getBattleLog($tag) {

        $battleLog = $this->client->getPlayerBattleLog($tag);
        return $battleLog ;
    }

    public function checkBattleTime($battle) {

        $battleTime = $battle['battleTime'];
    
        if ($battleTime > $this->lastTimeChecked)
         return true;

        else
         return false;
    }

    public function checkBattleType($battle) {

        $notRanked = !array_key_exists('type',$battle['battle']);
        if($notRanked)
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

        return false ;
    }

    public function checkBattle($battle, $data) { 
        
        $oldBattle = !$this->checkBattleTime($battle) ;
        if ($oldBattle)
          return 0;

        $validType = $this->checkBattleType($battle);

        if ($validType) {

           $key = $this->createKey($battle);
           $alreadyChecked = array_key_exists($key, $data);
             
           if (!$alreadyChecked)
            return 1;
        }

        else
          
          return 2 ;

    }

    public function isFirstTeam($players, $tag) {

        for ($i=0 ; $i<3 ; $i++){

            $player = $players[$i];
    
            if ($player['tag'] === '#'.$tag)
              
              return true ;
        }
         return false;

    }

    public function battleData($battle, $players, $tag){

        $mode = $battle['event']['mode'];
        $map = $battle['event']['map'];
        $result = $battle['battle']['result'];

        $data = [
            'tag' => $tag,
            'mode' => $mode,
            'map' => $map,
            'result' => $result
        ] ;
        
        $isFirstTeam = $this->isFirstTeam($players, $tag);

        $brawlers = array_map(fn($player) => $player['brawler']['name'], $players) ;

        $keys = array_map( fn($i) => 'brawler'.$i, range(1, 6));
        
        if (($isFirstTeam && $result === 'defeat' ) || (!$isFirstTeam && $result === 'victory')) {
          
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
    
       return $this->battleData($battle, $players, $tag) ;

    }
     
    public function createKey($battle){

        $tag = $battle['battle']['starPlayer']['tag'] ;
        $key = substr($battle['battleTime'],8,7).$tag ;
        return $key ;
    }

    public function checkBattleLog($tag, $data) { 
        
        $tagAlreadyChecked = array_search($tag,array_map(fn($element) => $element['tag'], $data));
        
        if ($tagAlreadyChecked)
            return $data;

        $battleLog = $this->getBattleLog($tag);

     
        for($i=0; $i<25; $i++) {

           $battle = $battleLog[$i];

           if ($this->checkBattle($battle, $data) === 0)
               return $data ;
              
           if($this->checkBattle($battle, $data) === 1) {
             
               $Key = $this->createKey($battle) ;
               $data[$Key] = $this->getBattleData($battle, $tag);
            }            
        }

        return $data ;
    }
 
}