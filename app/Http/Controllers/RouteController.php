<?php

namespace App\Http\Controllers;

use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        
        $routes = Route::all();
        return RouteResource::collection($routes); 
    }

    
    public function store(Request $request)
    {

        
       
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
          
        ]);

      
        $route = Route::create($validatedData);

       
        return new RouteResource($route); 
    }

    
    public function update(Request $request, Route $route)
    {
        
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
           
        ]);

     
        $route->update($validatedData);

       
        return new RouteResource($route); 
    }

    
    public function destroy(Route $route)
    {
       
        $route->delete();

        
        return response()->json(['message' => 'Route deleted successfully'], 200);
    }
}
