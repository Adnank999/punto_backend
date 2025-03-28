<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude', 'predefined_time', 'predefined_radius', 'predefined_direction'];


    

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot('route_order')
            ->withTimestamps();
    }

   
}
