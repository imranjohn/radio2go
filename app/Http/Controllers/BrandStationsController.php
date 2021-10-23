<?php

namespace App\Http\Controllers;

use App\Models\BrandStation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BrandStationsController extends Controller
{
    public function index()
    {
        return Inertia::render('BrandStations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'stations' => BrandStation::orderBy('id', 'desc')
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
        return Inertia::render('BrandStations/Create');
    }

    public function store()
    {
        BrandStation::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::route('brand-stations.index')->with('success', 'Brand station created.');
    }

    public function edit(BrandStation $brandStation)
    {
        return Inertia::render('BrandStations/Edit', [
            'station' => [
                'id' => $brandStation->id,
                'name' => $brandStation->name,
                'stream_url' => $brandStation->stream_url,
                'image_url' => $brandStation->image_url,
                'artwork_image' => $brandStation->artwork_image,
                'description' => $brandStation->description,
                'long_description' => $brandStation->long_description,
            ],
        ]);
    }

    public function update(BrandStation $brandStation)
    {
        $brandStation->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])
        );

        return Redirect::back()->with('success', 'Brand station updated.');
    }

    public function destroy(BrandStation $brandStation)
    {
        $brandStation->delete();

        return Redirect::route('brand-stations.index')->with('success', 'Brand station deleted.');
    }

    public function restore(BrandStation $brandStation)
    {
        $brandStation->restore();

        return Redirect::back()->with('success', 'Brand station restored.');
    }
}
