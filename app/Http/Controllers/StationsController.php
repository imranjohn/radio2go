<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\BrandStation;
use App\Models\SortedStation;
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
            'stations' => BrandStation::where('is_branded_station', false)->orderBy('id', 'desc')
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
        BrandStation::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])+['is_branded_station' => false]
        );

        return Redirect::route('stations.index')->with('success', 'Station created.');
    }

    public function edit(BrandStation $station)
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

    public function update(BrandStation $station)
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

    public function destroy(BrandStation $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $station->delete();

        return Redirect::route('stations.index')->with('success', 'Station deleted.');
    }

    public function restore(BrandStation $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $station->restore();

        return Redirect::back()->with('success', 'Station restored.');
    }


    public function stations() {
      
       // abort_if(!auth()->user()->owner, 403);
        Request::validate([
         'udid' => ['required'],
        ]);
      
        if(SortedStation::where('udid', request()->udid)->count()){
          //  $station = SortedStation::where('udid', request()->udid)->get();
           $station  = SortedStation::where('udid', request()->udid)->orderBy('sorted_number', 'asc')->get();
          
           $newStations = BrandStation::where('is_branded_station', false)->whereNotIn('id', $station->pluck('station_id')->toArray())->get();
           $sortedStation = [];
           foreach($newStations as $key => $value){

            $sortedStation[] = [
                'station_id' => $value->id,
                'udid' => request()->udid,
                'sorted_number' => 0,
            ];
           }
           
           if(!empty($sortedStation)){
            SortedStation::insert($sortedStation);
           }
         
           $station  = SortedStation::where('udid', request()->udid)->orderBy('sorted_number', 'asc')->get();
           $stations =  StationResource::collection($station);
        } else {

            $stations = BrandStation::where('is_branded_station', false)->get();
            foreach($stations as $key => $value){

                $sortedStation[] = [
                    'station_id' => $value->id,
                    'udid' => request()->udid,
                    'sorted_number' => $key+1,
                ];
            }

            SortedStation::insert($sortedStation);
            $station  = SortedStation::where('udid', request()->udid)->orderBy('sorted_number', 'asc')->get();
            $stations =  StationResource::collection($station);
           
        }
       
        $data = [
            'station' => $stations
        ];
        return response()->json($data);
    }

    public function sortStations() {

        Request::validate([
            'udid' => ['required'],
            'sorted_stations' => ['required']
        ]);

        $received_stations = json_decode(request()->sorted_stations);

        foreach($received_stations->stations as $key => $value){

            $sortedStation[] = [
                'station_id' => $value->station_id,
                'udid' => request()->udid,
                'sorted_number' => $value->sort_number,
            ];
        }
        SortedStation::where('udid', request()->udid)->delete();
        SortedStation::insert($sortedStation);
        $station  = SortedStation::where('udid', request()->udid)->orderBy('sorted_number', 'asc')->get();
        $stations =  StationResource::collection($station);
        $data = [
            'station' => $stations
        ];
        return response()->json($data);

    }

    public function duplicateStation(BrandStation $station) {
        abort_if(!auth()->user()->owner, 403);
        $new = $station->replicate();
        $new->name = "Duplicate of ($station->name)";
        $new->save();
        return Redirect::route('stations.index')->with('success', 'Station with a id of ('.$new->id.') has been duplicated.');

    }
}
