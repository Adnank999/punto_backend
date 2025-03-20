<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = ['bus_stop_id', 'route_id', 'predefined_time', 'route_order', 'recent_bus_stop_match'];

    public function busStop()
    {
        return $this->belongsTo(BusStop::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
