<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Services\SearchService;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return view('locations.index', compact('locations'));
    }

    public function search(Request $request, SearchService $service)
    {
        $query = $request->query('query', '');
        $locations = Location::all()->toArray();
        $result = $service->searchAndSort($locations, $query, ['name', 'address'], 'name');
        return response()->json($result);
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:locations,name',
            'address' => 'nullable|string',
        ]);

        Location::create($request->all());

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|unique:locations,name,' . $location->id,
            'address' => 'nullable|string',
        ]);

        $location->update($request->all());

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
