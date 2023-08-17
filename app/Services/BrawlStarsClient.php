<?php 

namespace App\Services ;


class BrawlStarsClient 
{
    private $apiUrl = 'https://api.brawlstars.com/v1/';
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getPlayer(string $playerTag)
    {
        $data = $this->request("players/%23{$playerTag}");

        return $data;
    }

    public function getPlayerBattleLog(string $playerTag)
    {
        $data = $this->request("players/%23{$playerTag}/battlelog");

        return $data['items'];
    }

    

    public function getEvents()
    {
        $data = $this->request('events/rotation');
        
        return $data;
    }

    public function getBrawlers()
    {
        $data = $this->request('brawlers');
        
        return $data['items'];
    }

    private function request(string $route): array
    {
        $ch = curl_init($this->apiUrl . $route);

        $headers = [];
        $headers[] = "Accept: application/json";
        $headers[] = "Authorization: Bearer " . $this->token;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        
        curl_close($ch);

        return json_decode($response, true);
    }
}