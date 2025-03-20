<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude'];

    public function busSchedules()
    {
        return $this->hasMany(BusStopSchedule::class);
    }

    public function buses()
    {
        return $this->belongsToMany(Bus::class, 'bus_stop_schedules')->withPivot('expected_arrival_time', 'route_stop_order');
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot('predefined_time', 'route_order')
            ->withTimestamps();
    }
}
