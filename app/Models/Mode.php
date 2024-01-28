<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    use HasFactory;

    public function scopeFilter($query, $modeName) {

        if($modeName === 'all') 
          
            return $query->orderBy('pick_rate_rank');
        else   
            
            return $query->where('name', 'like', $modeName);
  
  
      }
}
