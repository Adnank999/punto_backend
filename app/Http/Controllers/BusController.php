<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusResource;
use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::with('route')->get();
        return BusResource::collection($buses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gps_device_id' => 'required|string',
            'route_id' => 'required|exists:routes,id',
        ]);

        $bus = Bus::create($request->all());
        return new BusResource($bus);
    }

    public function update(Request $request, Bus $bus)
    {
        $request->validate([
            'gps_device_id' => 'string',
            'route_id' => 'exists:routes,id',
        ]);

        $bus->update($request->all());
        return new BusResource($bus);
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();
        return response()->json(['message' => 'Bus deleted successfully.']);
    }
}