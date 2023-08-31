<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brawler;
use App\Models\Mode;

class BrawlerMode extends Model
{
    use HasFactory;
    public function scopeFilter($query, $modeName, $brawlerName) {

    if ($brawlerName === 'all') {
            
        $modeId = Mode::where('name', 'like', $modeName)->first()->id ;
        return $query->where('mode_id', 'like', $modeId)->orderBy('win_rate_rank');
    }
    else {

        $modeId = Mode::where('name', 'like', $modeName)->first()->id ;
        $brawlerId = Brawler::where('name', 'like', $brawlerName)->first()->id ;
        return $query->where('brawler_id', 'like', $brawlerId)->where('mode_id', 'like', $modeId);
    }

}
public function brawler() {

    return $this->belongsTo(Brawler::class);
}

}