<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasFactory;

    public function scopeFilter($query, $mapName) {

        if($mapName === 'all') 
          
            return $query->orderBy('pick_rate_rank');
        else   
            return $query->where('name', 'like', $mapName);
  
      }

    public function mode(){

       return $this->belongsTo(Mode::class);
    }  
}
