<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brawler extends Model
{
    public function scopeFilter($query, $brawlerName) {

      if($brawlerName === 'all') 
        
          return $query->orderBy('win_rate_rank');
      else   
          
          return $query->where('name', 'like', $brawlerName);


    }
    use HasFactory;
}
