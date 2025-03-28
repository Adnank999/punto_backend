<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function busStops()
    {
        return $this->belongsToMany(BusStop::class, 'route_stops')
            ->withPivot('route_order')
            ->withTimestamps();
    }

    
}
