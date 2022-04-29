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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

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
        $current_time = time();
        if (Request::file('video')) {
            $video = request()->file('video');
            $name = $video->getClientOriginalName();
            $extension = $video ->getClientOriginalExtension();
            $video = request()->file('video')->storeAs('background_videos', $current_time.'-'.$name);
            $video_link = 'storage/'.$video;
            $getID3 = new \getID3;
            $ThisFileInfo = $getID3->analyze($video_link);
           
            $seconds = $ThisFileInfo['playtime_seconds'];

            if($seconds > 31){
                unlink($video_link);
               return redirect()->back()->with('error', 'Video is longer than 30 seconds');
            }
           
        }
    
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
            $photo = request()->file('photo');
            $name = $photo->getClientOriginalName();
            $extension = $photo->getClientOriginalExtension();
           $logo_url = request()->file('photo')->storeAs('photos', $brandStation->id.'-'.$name);
           $brandStation->update(['logo_url' => $logo_url]);
        }

        if (Request::file('background')) {
            $background = request()->file('background');
            $name = $background->getClientOriginalName();
            $extension = $background ->getClientOriginalExtension();
           $html_background_image = request()->file('background')->storeAs('html_background', $brandStation->id.'-'.$name);
           $brandStation->update(['html_background_image' => $html_background_image]);
        }

        if (Request::file('video')) {
            $video = request()->file('video');
            $name = $video->getClientOriginalName();
            $extension = $video ->getClientOriginalExtension();
           $video = request()->file('video')->storeAs('background_videos', $current_time.'-'.$name);
           $brandStation->update(['video_url' => $video]);
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
        $video_link = isset($brandStation->video_url) && $brandStation->video_url ? url('storage/'.optional($brandStation)->video_url) : null;
        if (file_exists(public_path('/storage/'.$brandStation->logo_url)) && $brandStation->logo_url) {
            $logo_url_link = url('storage/'.optional($brandStation)->logo_url);
        } elseif(file_exists( public_path() . '/storage/photos/'.$brandStation->id)){
            $logo_url_link = url('storage/photos/'.$brandStation->id);
        } else {
            $logo_url_link = null;
        }

        if (file_exists(public_path('/storage/'.$brandStation->html_background_image)) && $brandStation->html_background_image) {
            $html_background_image = url('storage/'.optional($brandStation)->html_background_image);
        } else {
            $html_background_image = null;
        }
       // $logo_url_link = isset($brandStation->logo_url) && $brandStation->logo_url ? url('storage/'.optional($brandStation)->logo_url) : null;
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
                'logo_url_link' => $logo_url_link,
                'logo_name' => $brandStation->logo_url,
                'html_background_image' => $html_background_image,
                'is_active' => $brandStation->is_active,
                'video_url' => $video_link,
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

        $current_time = time();
        if (Request::file('video')) {
            $video = request()->file('video');
            $name = $video->getClientOriginalName();
            $extension = $video ->getClientOriginalExtension();
            $video = request()->file('video')->storeAs('background_videos', $current_time.'-'.$name);
            $video_link = 'storage/'.$video;
            $getID3 = new \getID3;
            $ThisFileInfo = $getID3->analyze($video_link);
            
            $seconds = $ThisFileInfo['playtime_seconds'];

            if($seconds > 31){
                unlink($video_link);
               return redirect()->back()->with('error', 'Video is longer than 30 seconds');
            }
           
        }

        if (Request::file('photo')) {
            $photo = request()->file('photo');
            $name = $photo->getClientOriginalName();
            $extension = $photo->getClientOriginalExtension();
            $logo_url = request()->file('photo')->storeAs('photos', $brandStation->id.'-'.$name);
            $brandStation->update(['logo_url' => $logo_url]);
        }

        if (Request::file('background')) {
        
            $background = request()->file('background');
            $name = $background->getClientOriginalName();
            $extension = $background ->getClientOriginalExtension();
           $html_background_image = request()->file('background')->storeAs('html_background', $brandStation->id.'-'.$name);
           $brandStation->update(['html_background_image' => $html_background_image]);
        }

        if (Request::file('video')) {
            $video = request()->file('video');
            $name = $video->getClientOriginalName();
            $extension = $video ->getClientOriginalExtension();
           $video = request()->file('video')->storeAs('background_videos', $current_time.'-'.$name);
           $brandStation->update(['video_url' => $video]);
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
    
        if (file_exists(public_path('/storage/'.$brandStation->logo_url)) && $brandStation->logo_url) {
            $path = 'storage/'.$brandStation->logo_url;
        } elseif(file_exists( public_path() . '/storage/photos/'.$brandStation->id)){
            $path = 'storage/photos/'.$brandStation->id;
        } else {
            $path = 'logo_round.png';
        }
           // return response()->download($image);
        return view('mobile', compact('deep_link', 'path'));
         
     }

     public function brandStations(BrandStation $brandStation) {
        

         // abort_if(!auth()->user()->owner, 403);
                Request::validate([
                    'udid' => ['required'],
                ]);
               
               if($brandStation->deleted_at || !(boolean)$brandStation->is_active){
                    return response()->json(['message' => 'Station does not exists']);
               }

               $sortedStation[] = [
                'station_id' => $brandStation->id,
                'udid' => request()->udid,
                'sorted_number' => 0,
               ];

               $count = SortedStation::where('station_id', $brandStation->id)->where('udid', request()->udid)->whereHas('station', function(Builder $query){
                //$query->where('is_active', true);
               })->count();
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

     public function toggleStatus(BrandStation $brandStation) {
    
        $brandStation->update(['is_active' => !$brandStation->is_active]);    
        
        return Redirect::back()->with('success', 'Station updated.');
     }

     public function deleteVideoLink(BrandStation $brandStation) {
     
        $link = $brandStation->video_url;
       if($brandStation->update(['video_url' => null])){
        unlink(storage_path('app/public/'.$link));
       } 

        return Redirect::back()->with('success', 'Video link deleted.');
     }
}
