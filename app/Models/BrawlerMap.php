<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Map;
use App\Models\Brawler;
class BrawlerMap extends Model
{
    use HasFactory;

    public function scopeFilter($query, $mapName, $brawlerName)
    {
    
        
        if ($brawlerName === 'all') {
            
            $mapId = Map::where('name', 'like', $mapName)->first()->id ;
            return $query->where('map_id', 'like', $mapId)->orderBy('win_rate_rank');
        }
        else {
            $mapId = Map::where('name', 'like', $mapName)->first()->id ;
            $brawlerId = Brawler::where('name', 'like', $brawlerName)->first()->id ;
            return $query->where('brawler_id', 'like', $brawlerId)->where('map_id', 'like', $mapId);
        }
    }
    public function brawler(){

        return $this->belongsTo(Brawler::class);
    }



}
