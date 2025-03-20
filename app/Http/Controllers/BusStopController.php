<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusStopResource;
use App\Models\BusStop;
use Illuminate\Http\Request;

class BusStopController extends Controller
{
    public function index()
    {
        $busStops = BusStop::all();
        return BusStopResource::collection($busStops); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

       
        $busStop = BusStop::create($request->all());
        return new BusStopResource($busStop); 
    }

    public function update(Request $request, BusStop $busStop)
    {
        $request->validate([
            'name' => 'string|max:255',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
        ]);

        $busStop->update($request->all());
        return new BusStopResource($busStop); 
    }

    public function destroy(BusStop $busStop)
    {
        $busStop->delete();
        return response()->json(['message' => 'Bus stop deleted successfully.']);
    }
}