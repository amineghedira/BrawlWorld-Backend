<?php

namespace App\Services ;

use App\Services\BrawlStarsClient ;
use Illuminate\Support\Facades\Log;


class BrawlStarsService {

    // Properties 
    private $lastTimeChecked;
    private $client ;

    // Constructor
    public function __construct($apitoken) {

           $this->client = new BrawlStarsClient($apitoken);
           $this->lastTimeChecked = $this->lastTimeChecked();
    }
    
    // Methods
    
    // returns the time we last sampled a collection of battles
    function lastTimeChecked() {

     $format = 'Ymd\THis\.000\Z';
     $timestamp = time() - (5 * 3600) - (55 * 60); // Current timestamp minus 5 hours and 55 minutes

     return gmdate($format, $timestamp);
    }

    //get battleLog a player with a certain tag
    public function getBattleLog($tag) {

        $battleLog = $this->client->getPlayerBattleLog($tag);
        return $battleLog ;
    }
    
    //check whether the battle has been played after we collected our last sample
    public function checkBattleTime($battle) {

        $battleTime = $battle['battleTime'];
    
        if ($battleTime > $this->lastTimeChecked)
         return true;

        else
         return false;
    }

    //validate if the battl's mode is one of the main competitive modes
    public function validMode($mode) {

       $validModes = ['brawlBall','gemGrab','hotZone','heist','bounty','knockout'];

       if (array_search($mode, $validModes) === false)
           return false ;
       else
           return true ;
        
    }

    //check if the battle is ranked and and not friendly or a challenge battle
    public function checkBattleType($battle) {

        $notRanked = !array_key_exists('type',$battle['battle']);
        if($notRanked)
            return false;

        $type = $battle['battle']['type'] ;
        $mode = $battle['battle']['mode'];
        $map = $battle['event']['map'] ;

        if ($type ==='ranked') {

          if( !$this->validMode($mode) || $map === null )
            return false;

          else
            return true;  
        }

        return false ;
    }

    //check if the battle is overall eligible for our sample requirements
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
          
        return 2 ;

    }

   // check if the player with the current tag is in the first team
    public function isFirstTeam($players, $tag) {

        for ($i=0 ; $i<3 ; $i++){

            $player = $players[$i];
    
            if ($player['tag'] === '#'.$tag)
              
              return true ;
        }
         return false;

    }

    public function formatMode($name) {
     $formatted = strtoupper($name[0]);
     $length = strlen($name);

     for ($i =1; $i<$length ; $i++){
        if(ctype_upper($name[$i]))

            $formatted = $formatted.'-'.$name[$i];
        else
            $formatted = $formatted.$name[$i];
     }
     return $formatted;

    }
    public function formatBrawler($name) {
        $formatted= $name[0].strtolower(substr($name,1));
        return $formatted;

    }
    
    // returns the relevant information we need about the battle
    public function battleData($battle, $players, $tag){

        $mode = $battle['event']['mode'];
        $mode = $this->formatMode($mode);
        $map = $battle['event']['map'];
        $map = str_replace(" ", "-", $map);
       $result = $battle['battle']['result'];


        $data = [
            'mode' => $mode,
            'map' => $map,
            'result' => $result
        ] ;
        
        $isFirstTeam = $this->isFirstTeam($players, $tag);

        $brawlers = array_map(fn($player) => $this->formatBrawler($player['brawler']['name']), $players) ;

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
    
    // returns an array of battles data
    public function getBattleData($battle, $tag) {

       $players = array_merge(...$battle['battle']['teams']);
    
       return $this->battleData($battle, $players, $tag) ;

    }
     
    //create a unique key for each battle , so we don't double count it in the future
    public function createKey($battle){

        $tag = $battle['battle']['starPlayer']['tag'] ;
        $key = substr($battle['battleTime'],8,7).$tag ;
        return $key ;
    }

    // check the battleLog of a player, and adds any relevant information about his battles to our sample array
    public function checkBattleLog($tag, $data) { 
        
        $battleLog = $this->getBattleLog($tag);

        if($battleLog === null)
            return $data ;

        $n = count($battleLog);

        for($i=0; $i<$n; $i++) {

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

    // takes a tag of player and returns another random tag
    public function getRandomTag($tag) {

        $battleLog = $this->getBattleLog($tag);

        if($battleLog === null)
            return env('ORIGINAL_TAG') ;

        $n = count($battleLog) ;
        $counter=0;
        
        do {

        $counter++ ;
        if ($counter > 10)
           return env('ORIGINAL_TAG') ;
        $randomBattle = mt_rand(0,$n-1) ;
        $battle = $battleLog[$randomBattle]['battle'] ;
        $notRanked = !array_key_exists('type',$battle) || $battle['type'] === 'friendly' ;

        } while($notRanked );


        if (array_key_exists('teams', $battle)) {

           $players = array_merge(...$battle['teams'])  ;
           $randomPlayer = mt_rand(0,5); 
        }   

        else if ($battle['mode']==='soloShowdown' ) {

           $players = $battle['players'];
           $randomPlayer = mt_rand(0,9); 
        }

        else {
           $players = $battle['players'];
           $randomPlayer = mt_rand(0,1);
        }

        $randomTag = $players[$randomPlayer]['tag'];


        if (strlen($randomTag) < 6)
           return $tag ;

        else 
           return $randomTag;
        
    }

    // takes a tag of player and returns a new tag that we didn't check before
    public function getNewTag($tag, $tags) {
       
       $randomTag = $this->getRandomTag($tag);
       $tagAlreadyChecked = array_search($randomTag, $tags); 
       $counter = 0;

       while($tagAlreadyChecked !== false || !$this->getBattleLog($randomTag)) {

        if ($counter > 10) {
          $randomTag = env('ORIGINAL_TAG');
          $counter = 0 ;
        }

        $randomTag = $this->getRandomTag($randomTag);
        $tagAlreadyChecked = array_search($randomTag, $tags);
        $counter++ ;
       }
       
       return $randomTag ;   
    }
   
    //this function will be used for command.
    //it takes the tag we will start with, the size of our sample,and returns n battles with their information.
    public function getSampleData($tag, $n) {
        print_r('oy');
        $data=[] ;
        $tags=[] ;
        $sampleSize = $n ;
        $count = 0;
        while(count($data) < $sampleSize) {
            $count++ ;
            $data = $this->checkBattleLog($tag, $data);
        
            array_push($tags, $tag) ;  
            $tag = $this->getNewTag($tag, $tags);
            print_r('iteration '.$count);
        } 
        Log::info('BrawlStarsService completed after '.$count.' iterations' ) ;
        return $data ;
        

    }



 
}