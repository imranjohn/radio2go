<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SortedStation extends Model
{
    use HasFactory;

    public function station(){
        return $this->belongsTo(BrandStation::class, 'station_id');
    }
}
