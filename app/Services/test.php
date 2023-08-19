<?php

namespace App\Services ;

require 'App/Services/BrawlStarsServices.php' ;
use App\Services\BrawlStarsServices ;

$apitoken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjFiODFiZTdlLTQxMTEtNDZhYi04YzZhLWZiYzFlYTI1ZWZhMiIsImlhdCI6MTY5MjE3OTQxMCwic3ViIjoiZGV2ZWxvcGVyLzBmZDBlNDZkLTliZTEtZTQzMC1lODZjLTI2YWRjZDdmYzA5NyIsInNjb3BlcyI6WyJicmF3bHN0YXJzIl0sImxpbWl0cyI6W3sidGllciI6ImRldmVsb3Blci9zaWx2ZXIiLCJ0eXBlIjoidGhyb3R0bGluZyJ9LHsiY2lkcnMiOlsiMTk3LjI2LjEwMi4xODMiXSwidHlwZSI6ImNsaWVudCJ9XX0.u-x7q40B3KIbfJePVRJkQ8bKToxp9Lj0p64TdcaWFtOCiJWhaLzIvDbztuMNbUD73ChZAvjI050GGFkt45NV-w';
$apitoken2 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6ImZkZTYyMDdhLTI2NzItNGQwNC04ZWVjLWFiMDQ3MWQ2YzllYiIsImlhdCI6MTY5MjQ2NDY0NSwic3ViIjoiZGV2ZWxvcGVyLzBmZDBlNDZkLTliZTEtZTQzMC1lODZjLTI2YWRjZDdmYzA5NyIsInNjb3BlcyI6WyJicmF3bHN0YXJzIl0sImxpbWl0cyI6W3sidGllciI6ImRldmVsb3Blci9zaWx2ZXIiLCJ0eXBlIjoidGhyb3R0bGluZyJ9LHsiY2lkcnMiOlsiMTk3LjIuMTg1LjIxNiJdLCJ0eXBlIjoiY2xpZW50In1dfQ.54kGWlVbMALh6s40LNO1-EVEwCdD2anAadQu8lJLXG8SX68WFWs6BvYQvVCLS0f1xCFYAmtppXbVS3eeWlcC5g';
$array =[];
$tags =['9CQV09G2U','Y88CPU9V2','9CQV09G2U' ];
$client = new BrawlStarsServices($apitoken2);
foreach ($tags as $tag) {
$array = $array + $client->checkBattleLog($tag, $array);
}
print_r($array);

/*$client = new BrawlStarsClient($apitoken) ;
$tag = '9CQV09G2U';
$battleLog = $client->getPlayerBattleLog($tag);
$players = array_merge(...$battleLog[7]['battle']['teams']);
$map = $battleLog[7]['event']['map'];
$result = $battleLog[7]['battle']['result'];
 $data = [
            'map' => $map,
            'result' => $result
        ];
        $brawlers = array_map(fn($player) => $player['brawler']['name'], $players) ;

        $keys = array_map( fn($i) => 'brawler'.$i, range(1, 6));
        $brawlers = array_combine($keys, $brawlers);

///$battleLogs = $client->getPlayerBattleLog($tag);
print_r('array'.(1+1));*/
