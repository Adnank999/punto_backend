<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusStopScheduleResource;
use App\Models\BusStopSchedule;
use Illuminate\Http\Request;

class BusStopScheduleController extends Controller
{
    public function index()
    {
        $busStopSchedules = BusStopSchedule::with(['bus', 'busStop'])->get(); 

        return BusStopScheduleResource::collection($busStopSchedules); 
    }

    
    public function show($id)
    {
        $busStopSchedule = BusStopSchedule::with(['bus', 'busStop'])->findOrFail($id); 

        return new BusStopScheduleResource($busStopSchedule); 
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'bus_stop_id' => 'required|exists:bus_stops,id',
            'expected_arrival_time' => 'required|date_format:H:i',
            'route_stop_order' => 'required|integer',
        ]);

        $busStopSchedule = BusStopSchedule::create($validatedData);

        return new BusStopScheduleResource($busStopSchedule); 
    }


    public function update(Request $request, $id)
    {
        $busStopSchedule = BusStopSchedule::findOrFail($id);

        $validatedData = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'bus_stop_id' => 'required|exists:bus_stops,id',
            'expected_arrival_time' => 'required|date_format:H:i',
            'route_stop_order' => 'required|integer',
        ]);

        $busStopSchedule->update($validatedData);

        return new BusStopScheduleResource($busStopSchedule); 
    }


    public function destroy($id)
    {
        $busStopSchedule = BusStopSchedule::findOrFail($id);
        $busStopSchedule->delete();

        return response()->json(['message' => 'Bus stop schedule deleted successfully'], 200);
    }
}
