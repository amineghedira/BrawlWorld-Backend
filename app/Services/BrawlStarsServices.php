<?php

namespace App\Services ;

use App\Services\BrawlStarsClient ;

class BrawlStarsServices {

    // Properties 
    //private static $apitoken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjFiODFiZTdlLTQxMTEtNDZhYi04YzZhLWZiYzFlYTI1ZWZhMiIsImlhdCI6MTY5MjE3OTQxMCwic3ViIjoiZGV2ZWxvcGVyLzBmZDBlNDZkLTliZTEtZTQzMC1lODZjLTI2YWRjZDdmYzA5NyIsInNjb3BlcyI6WyJicmF3bHN0YXJzIl0sImxpbWl0cyI6W3sidGllciI6ImRldmVsb3Blci9zaWx2ZXIiLCJ0eXBlIjoidGhyb3R0bGluZyJ9LHsiY2lkcnMiOlsiMTk3LjI2LjEwMi4xODMiXSwidHlwZSI6ImNsaWVudCJ9XX0.u-x7q40B3KIbfJePVRJkQ8bKToxp9Lj0p64TdcaWFtOCiJWhaLzIvDbztuMNbUD73ChZAvjI050GGFkt45NV-w';
    private $client ;

    // Constructor
    public function __construct ($apitoken) {
        $this->client = new BrawlStarsClient($apitoken) ;
    }

    // Methods
    public function getBattleLog($tag) {

        $battleLog = $client->getPlayerBattleLog($tag);
        return $battleLog ;
    }

    public function checkBattleDate($battleLog, $battleIndex) {

        $battleTime = $battleLog[$battleIndex]['battleTime'];
        $battleDate = substr($battleTime,0,8);
        $currentDate = gmdate('Ymd');
    
        if ($battleDate===$currentDate)
         return true;

        else
         return false;
    }

    public function checkBattle($battleLog, $battleIndex) {
        

    }

    public function isFirstTeam($teams, $tag){
        for ($i=0 ; $i<6 ; $i++){

            $player = $teams[i];
    
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
        ];
        $brawlers = array_map(fn($player) => $player['brawler']['name'], $players) ;

        if (($isFirstTeam && $result === 'defeat' ) || ($isFirstTeam && $result === 'victory'))
          
         return array_merge($data, array_reverse($brawlers));
        
        else

         return array_merge($data, $brawlers);
        
    }

    public function getBattleData($battleLog, $index, $tag) {

    $battle = $battleLog[$index];
    $players = array_merge(...$battle['battle']['teams']);

    

    $isFirstTeam = isFirstTeam($players, $tag);
    
    return battleData($battle, $players, $isFirstTeam);

    }
    

    public function checkBattleLog($tag) {

     $Data = [];
     $battleLog = getBattleLog($tag)

     //check whether the latest Battle is of today's date.
     if (checkBattle($battleLog, 0)){
        
        $Data["tag"] = $tag;
        $Data["battle1"]=getBattleInfo($battleLog, 0, $tag) ;

        for($i=1; i<25; $i++){
        
          if(checkBattle($battleLog, $i))
            

          


      }
    

     
  }
 

}

}