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
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

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
        
        $brandStation =  BrandStation::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])+['is_branded_station' => false]
        );

        if (Request::file('background')) {
            $background = request()->file('background');
            $name = $background->getClientOriginalName();
            $extension = $background ->getClientOriginalExtension();
           $html_background_image = request()->file('background')->storeAs('html_background', $brandStation->id.'-'.$name);
           $brandStation->update(['html_background_image' => $html_background_image]);
        }

        return Redirect::route('stations.index')->with('success', 'Station created.');
    }

    public function edit(BrandStation $station)
    {
        abort_if(!auth()->user()->owner, 403);

        if (file_exists(public_path('/storage/'.$station->html_background_image)) && $station->html_background_image) {
            $html_background_image = url('storage/'.optional($station)->html_background_image);
        } else {
            $html_background_image = null;
        }
        return Inertia::render('Stations/Edit', [
            'station' => [
                'id' => $station->id,
                'name' => $station->name,
                'stream_url' => $station->stream_url,
                'image_url' => $station->image_url,
                'artwork_image' => $station->artwork_image,
                'description' => $station->description,
                'long_description' => $station->long_description,
                'html_background_image' => $html_background_image,
                'is_active' => $station->is_active,
            ],
        ]);
    }

    public function update(BrandStation $station)
    {
        abort_if(!auth()->user()->owner, 403);
        dd(request()->all());
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

        if (Request::file('background')) {
        
            $background = request()->file('background');
            $name = $background->getClientOriginalName();
            $extension = $background ->getClientOriginalExtension();
           $html_background_image = request()->file('background')->storeAs('html_background', $brandStation->id.'-'.$name);
           $station->update(['html_background_image' => $html_background_image]);
        }

        return Redirect::back()->with('success', 'Station updated.');
    }

    public function destroy(BrandStation $station)
    {
        abort_if(!auth()->user()->owner, 403);
        $id = $station->id;
        $station->delete();
        SortedStation::where('station_id', $id)->delete();
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
        $stationCount = SortedStation::where('udid', request()->udid)->count();
        if($stationCount){
          //  $station = SortedStation::where('udid', request()->udid)->get();
           $station  = SortedStation::where('udid', request()->udid)->orderBy('sorted_number', 'asc')->get();
          
           $newStations = BrandStation::where('is_branded_station', false)->whereNotIn('id', $station->pluck('station_id')->toArray())->get();
           $sortedStation = [];
           foreach($newStations as $key => $value){

            $sortedStation[] = [
                'station_id' => $value->id,
                'udid' => request()->udid,
                'sorted_number' => $stationCount+($key+1),
            ];
           }
           
           if(!empty($sortedStation)){
            SortedStation::insert($sortedStation);
           }
         
           $station  = SortedStation::where('udid', request()->udid)->whereHas('station', function(Builder $query){
            $query->where('is_active', true);
           })->orderBy('sorted_number', 'asc')->get();
           $stations =  StationResource::collection($station);
        } else {

            $stations = BrandStation::where('is_branded_station', false)->where('is_active', true)->orderBy('name', 'asc')->get();
            foreach($stations as $key => $value){

                $sortedStation[] = [
                    'station_id' => $value->id,
                    'udid' => request()->udid,
                    'sorted_number' => $key+1,
                ];
            }

            SortedStation::insert($sortedStation);
            $station  = SortedStation::where('udid', request()->udid)->whereHas('station', function(Builder $query){
                $query->where('is_active', true);
            })->orderBy('sorted_number', 'asc')->get();
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
        // Log::info("====================== Request all ============");
        // Log::info(request()->all());
        // Log::info("====================== Request all end ============");


        $received_stations = json_decode(request()->sorted_stations);

        // Log::info("====================== Json decode all ============");
        // Log::info((array)$received_stations);
        // Log::info("====================== Json decode end ============");
        foreach($received_stations->stations as $key => $value){

            $sortedStation[] = [
                'station_id' => $value->station_id,
                'udid' => request()->udid,
                'sorted_number' => $value->sort_number,
            ];
        }

        // Log::info("====================== Sorted array ============");
        // Log::info($sortedStation);
        // Log::info("====================== Sorted array end ============");
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
