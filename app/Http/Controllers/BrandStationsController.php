<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\BrandStation;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use wapmorgan\Mp3Info\Mp3Info;
use App\Models\SortedStation;

class BrandStationsController extends Controller
{
    public function index()
    {
        return Inertia::render('BrandStations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'stations' => BrandStation::where('is_branded_station', true)->where(function($query){
                if(!auth()->user()->owner){
                    $query->where('created_by', auth()->user()->id);
                }
                
            })->orderBy('id', 'desc')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($station) => [
                    'id' => $station->id,
                    'name' => $station->name,
                    'deep_link' => $station->deep_link,
                    'stream_url' => $station->stream_url,
                    'image_url' => $station->image_url,
                    'description' => $station->description,
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
    
    
       $brandStation = BrandStation::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'stream_url' => ['required', 'max:200'],
                'image_url' => ['required', 'url'],
                'artwork_image' => ['nullable'],
                'description' => ['required'],
                'long_description' => ['nullable'],
            ])
        );

        if (Request::file('photo')) {
          request()->file('photo')->storeAs('photos', $brandStation->id);
        }

        $audio_url = "";
        $audio_duration = 0;
        if (Request::file('audio')) {
            $audio = request()->file('audio');
            $name = $audio->getClientOriginalName();
            $extension = $audio->getClientOriginalExtension();
            $audio_url =  request()->file('audio')->storeAs('audio', time().'-'.$name);
           $audio = new Mp3Info('storage/'.$audio_url);
           $audio_duration = $audio->duration;

        }


       $id = $brandStation->id;

       $key = 'AIzaSyCnL_gj4W4P4B9snOFw_thX7Yb5EXwPWrA';
       $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $key;
     
       $data = array(
          "dynamicLinkInfo" => array(
             "domainUriPrefix" => "radio2go.page.link",
             "link" => route('brand.stations.deeplink', ['brandStation' => $id]),
             "iosInfo" => [
                 "iosBundleId" => "com.letech.radio2go",
                 "iosAppStoreId" => "1588788883"
             ],
             "androidInfo" => [
                 "androidPackageName" => "com.app.radio2go"
             ]
          )
       );
     
     
     
     
       $headers = array('Content-Type: application/json');
     
       $ch = curl_init ();
       curl_setopt ( $ch, CURLOPT_URL, $url );
       curl_setopt ( $ch, CURLOPT_POST, true );
       curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
       curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
       curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
     
       $data = curl_exec ( $ch );
       curl_close ( $ch );
     
       $short_url = json_decode($data);
     

       if(isset($short_url->error)){
          return redirect()->back()->with('error', $short_url->error->message);
       } else {
        $shortLink = $short_url->shortLink;
        $brandStation->update(['deep_link' => $shortLink, 'audio_url' => $audio_url, 'audio_duration' => $audio_duration, 'created_by' => auth()->user() ? auth()->user()->id: 1]);

        if(auth()->user()){
            return Redirect::route('brand-stations.index')->with('success', 'Brand station created.');
        } else {
            return response()->json($brandStation);
        }
        
       }
    }
     

    public function edit(BrandStation $brandStation)
    {
       
        $audio_link = isset($brandStation->audio_url) && $brandStation->audio_url ? url('storage/'.optional($brandStation)->audio_url) : null;
        return Inertia::render('BrandStations/Edit', [
            'station' => [
                'id' => $brandStation->id,
                'name' => $brandStation->name,
                'stream_url' => $brandStation->stream_url,
                'image_url' => $brandStation->image_url,
                'artwork_image' => $brandStation->artwork_image,
                'description' => $brandStation->description,
                'long_description' => $brandStation->long_description,
                'photoExist' => file_exists( public_path() . '/storage/photos/'.$brandStation->id) ? true : false,
                'audio_url' => $audio_link,
                'audio_name' => $brandStation->audio_url,
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
        if (Request::file('photo')) {
            request()->file('photo')->storeAs('photos', $brandStation->id);
        }

        $audio_url = "";
        $audio_duration = 0;
        if (Request::file('audio')) {

            $audio = request()->file('audio');
            $name = $audio->getClientOriginalName();
            $extension = $audio->getClientOriginalExtension();
            $audio_url =  request()->file('audio')->storeAs('audio', time().'-'.$name);

           $audio = new Mp3Info('storage/'.$audio_url);
           $audio_duration = $audio->duration;
           $brandStation->update(['audio_url' => $audio_url, 'audio_duration' => $audio_duration]);
        }

       
        return Redirect::back()->with('success', 'Brand station updated.');
    }

    public function destroy(BrandStation $brandStation)
    {
        $id = $brandStation->id;
        $brandStation->delete();
        SortedStation::where('station_id', $id)->delete();
        return Redirect::route('brand-stations.index')->with('success', 'Brand station deleted.');
    }

    public function restore(BrandStation $brandStation)
    {
        
        $brandStation->restore();

        return Redirect::back()->with('success', 'Brand station restored.');
    }


    public function qrCodeGenerator(BrandStation $brandStation) {

        $brand_name = 'brand_stations';
        $deep_link = $brandStation->deep_link;
       
        $path = 'logo_round.png';
        if (file_exists( public_path() . '/storage/photos/'.$brandStation->id)) {
            $path = 'storage/photos/'.$brandStation->id;
        } 
           // return response()->download($image);
        return view('mobile', compact('deep_link', 'path'));
         
     }

     public function brandStations(BrandStation $brandStation) {
        

         // abort_if(!auth()->user()->owner, 403);
                Request::validate([
                    'udid' => ['required'],
                ]);
         
               $sortedStation[] = [
                'station_id' => $brandStation->id,
                'udid' => request()->udid,
                'sorted_number' => 0,
               ];

               $count = SortedStation::where('station_id', $brandStation->id)->where('udid', request()->udid)->count();
               if($count < 1){
                SortedStation::insert($sortedStation);
               }
               
               request()->merge(['isFavorite' => true]);
               $station_res = new StationResource($brandStation);
                return response()->json($station_res);
     
     }

     public function duplicateBrandStation(BrandStation $brandStation) {
        $new = $brandStation->replicate();
        $new->name = "Duplicate of ($brandStation->name)";
        $new->save();

        $id = $new->id;

        $key = 'AIzaSyCnL_gj4W4P4B9snOFw_thX7Yb5EXwPWrA';
        $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $key;
      
        $data = array(
           "dynamicLinkInfo" => array(
              "domainUriPrefix" => "radio2go.page.link",
              "link" => route('brand.stations.deeplink', ['brandStation' => $id]),
              "iosInfo" => [
                  "iosBundleId" => "com.letech.radio2go",
                  "iosAppStoreId" => "1588788883"
              ],
              "androidInfo" => [
                  "androidPackageName" => "com.app.radio2go"
              ]
           )
        );
      
      
      
      
        $headers = array('Content-Type: application/json');
      
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
      
        $data = curl_exec ( $ch );
        curl_close ( $ch );
      
        $short_url = json_decode($data);
      
 
        if(isset($short_url->error)){
           return redirect()->back()->with('error', $short_url->error->message);
        } else {
         $shortLink = $short_url->shortLink;
         $new->update(['deep_link' => $shortLink]);
         return Redirect::route('brand-stations.index')->with('success', 'Brand station with a id of ('.$new->id.') has been duplicated.');
        }

       
       
     }
}
