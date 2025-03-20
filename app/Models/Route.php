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

    public function busStopSchedules()
    {
        return $this->hasMany(BusStopSchedule::class);
    }

    // public function busStops()
    // {
    //     return $this->belongsToMany(BusStop::class, 'bus_stop_schedules', 'route_id', 'bus_stop_id');
    // }


    public function busStops()
    {
        return $this->belongsToMany(BusStop::class, 'route_stops')
            ->withPivot('predefined_time', 'route_order')
            ->withTimestamps();
    }

    
}
