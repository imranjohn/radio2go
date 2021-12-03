<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StationsController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->owner, 403);
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
                    'description' => $station->description,
                    'deleted_at' => $station->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        abort_if(!auth()->user()->owner, 403);
        return Inertia::render('Stations/Create');
    }

    public function store()
    {
        abort_if(!auth()->user()->owner, 403);
        Station::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::route('stations.index')->with('success', 'Station created.');
    }

    public function edit(Station $station)
    {
        abort_if(!auth()->user()->owner, 403);
        return Inertia::render('Stations/Edit', [
            'station' => [
                'id' => $station->id,
                'name' => $station->name,
                'stream_url' => $station->stream_url,
                'image_url' => $station->image_url,
                'artwork_image' => $station->artwork_image,
                'description' => $station->description,
                'long_description' => $station->long_description,
            ],
        ]);
    }

    public function update(Station $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $station->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::back()->with('success', 'Station updated.');
    }

    public function destroy(Station $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $station->delete();

        return Redirect::route('stations.index')->with('success', 'Station deleted.');
    }

    public function restore(Station $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $station->restore();

        return Redirect::back()->with('success', 'Station restored.');
    }


    public function stations() {
       // abort_if(!auth()->user()->owner, 403);
        
        $station = StationResource::collection(Station::all());

        $data = [
            'station' => $station
        ];
        return response()->json($data);
    }

    public function duplicateStation(Station $station) {
        abort_if(!auth()->user()->owner, 403);
        $new = $station->replicate();
        $new->save();
        return Redirect::route('stations.index')->with('success', 'Station with a id of ('.$new->id.') has been duplicated.');

    }
}
