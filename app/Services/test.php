<?php

namespace App\Services ;

require 'App/Services/BrawlStarsClient.php' ;
use App\Services\BrawlStarsClient ;

$apitoken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjFiODFiZTdlLTQxMTEtNDZhYi04YzZhLWZiYzFlYTI1ZWZhMiIsImlhdCI6MTY5MjE3OTQxMCwic3ViIjoiZGV2ZWxvcGVyLzBmZDBlNDZkLTliZTEtZTQzMC1lODZjLTI2YWRjZDdmYzA5NyIsInNjb3BlcyI6WyJicmF3bHN0YXJzIl0sImxpbWl0cyI6W3sidGllciI6ImRldmVsb3Blci9zaWx2ZXIiLCJ0eXBlIjoidGhyb3R0bGluZyJ9LHsiY2lkcnMiOlsiMTk3LjI2LjEwMi4xODMiXSwidHlwZSI6ImNsaWVudCJ9XX0.u-x7q40B3KIbfJePVRJkQ8bKToxp9Lj0p64TdcaWFtOCiJWhaLzIvDbztuMNbUD73ChZAvjI050GGFkt45NV-w';
$client = new BrawlStarsClient($apitoken) ;
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

///$battleLogs = $client->getPlayerBattleLog($tag);
print_r(array_merge($data, array_reverse($brawlers)));
