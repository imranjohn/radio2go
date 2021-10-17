<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Stations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'stations' => Station::orderBy('id', 'desc')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($station) => [
                    'id' => $station->id,
                    'name' => $station->name,
                    'stream_url' => $station->stream_url,
                    'image_url' => $station->image_url,
                    'deleted_at' => $station->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Stations/Create');
    }

    public function store()
    {
        Station::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['nullable', 'max:200'],
                'image_url' => ['nullable'],
                'description' => ['nullable'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::route('stations.index')->with('success', 'Station created.');
    }

    public function edit(Station $station)
    {
        return Inertia::render('Stations/Edit', [
            'station' => [
                'id' => $station->id,
                'name' => $station->name,
                'stream_url' => $station->stream_url,
                'image_url' => $station->image_url,
                'description' => $station->description,
                'long_description' => $station->long_description,
            ],
        ]);
    }

    public function update(Station $station)
    {
        $station->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['nullable', 'max:200'],
                'image_url' => ['nullable'],
                'description' => ['nullable'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::back()->with('success', 'Station updated.');
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return Redirect::route('stations.index')->with('success', 'Station deleted.');
    }

    public function restore(Station $station)
    {
        $station->restore();

        return Redirect::back()->with('success', 'Station restored.');
    }
}
