<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BrandStationsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StationsController;
use App\Http\Controllers\UsersController;
use App\Models\BrandStation;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Kreait\Firebase\DynamicLink\CreateDynamicLink;
use Kreait\Firebase\DynamicLink\CreateDynamicLink\FailedToCreateDynamicLink;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth
Route::middleware(['check.domain'])->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Dashboard

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Users

Route::get('users', [UsersController::class, 'index'])
    ->name('users')
    ->middleware('auth');

Route::get('users/create', [UsersController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');

Route::post('users', [UsersController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

    // Stations
    
Route::resource('stations', StationsController::class)->middleware('auth');;
Route::resource('brand-stations', BrandStationsController::class)->middleware('auth');

Route::get('qr-code-viewwer/{brandStation}', [BrandStationsController::class, 'qrCodeGenerator'])->name('brand-station.qrCodeGenerator');
Route::post('brand-stations/{brandStation}', [BrandStationsController::class, 'duplicateBrandStation'])->name('brand-stations.duplicate');
Route::post('brand-stations/update/{brandStation}', [BrandStationsController::class, 'update'])->name('brand-stations.updateNew');
Route::post('stations/update/{brandStation}', [BrandStationsController::class, 'update'])->name('stations.updateNew');
Route::post('stations/{station}', [StationsController::class, 'duplicateStation'])->name('stations.duplicate');
Route::post('brand-station-status/{brandStation}', [BrandStationsController::class, 'toggleStatus'])->name('brand-stations.toggleStation');

Route::post('brand-stations/delete-brand-video/{brandStation}', [BrandStationsController::class, 'deleteVideoLink'])->name('brand-stations.deleteVideoLink');

Route::get('generate-link', function() {

    dd(route('brand.stations.deeplink', ['brandStation' => 4]));
  
    // $dynamicLinks = app('firebase.dynamic_links');
    // $url = 'https://radio2go.page.link';

    // try {
    //     $link = $dynamicLinks->createUnguessableLink($url);
    //     $link = $dynamicLinks->createDynamicLink($url, CreateDynamicLink::WITH_UNGUESSABLE_SUFFIX);
    
    //     $link = $dynamicLinks->createShortLink($url);
    //     $link = $dynamicLinks->createDynamicLink($url, CreateDynamicLink::WITH_SHORT_SUFFIX);
    // } catch (FailedToCreateDynamicLink $e) {
    //     echo $e->getMessage(); exit;
    // }


    // dd($link);
    $key = 'AIzaSyCnL_gj4W4P4B9snOFw_thX7Yb5EXwPWrA';
  $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $key;

//   $data = [
//     "longDynamicLink" => "https://radio2go.page.link/?link="
//   ];
  $data = array(
     "dynamicLinkInfo" => array(
        "domainUriPrefix" => "radio2go.page.link",
        "link" => "http://appadmin.radio2go.fm/api/brand-stations?ids=1,4,3",
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
  dd($short_url);
  if(isset($short_url->error)){
      return $short_url->error->message;
  } else {
      return $short_url->shortLink;
  }

});

Route::get('apple-app-site-association', function() {
//{"applinks":{"apps":[],"details":[{"appID":"NRFC2SBT2K.com.letech.radio2go","paths":["*"]}]}}
$json = file_get_contents('.well-known/apple-app-site-association');
    return response($json, 200)
        ->header('Content-Type', 'application/json');

// $data = [
//     'applinks' => [
//         "apps" => [],
//         "details" => [
//             [
//                 "appID" => "NRFC2SBT2K.com.letech.radio2go",
//                 "paths" => ["*"]
//             ]
//         ]
//     ]
// ];
// return response()->json($data);
});



Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

// Images

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');
Route::get('/create-html-for-radio/{brandStation}', function(BrandStation $brandStation) {
    
    if($brandStation->artwork_image){
        $logo = $brandStation->artwork_image;
    } else {
        $logo = $brandStation->html_background_image ? url('storage/'.optional($brandStation)->logo_url): "https://appadmin.radio2go.fm/logo.png";
    }

    if($brandStation->audio_url){
        $audio_url = url('storage/'.$brandStation->audio_url);
    } else {
        $audio_url = $brandStation->stream_url;
    }
    
    if($brandStation->video_url){
             $video_url = url('storage/'.$brandStation->video_url);
        } else {
            $video_url = null;
        }
        
    $background_image = $brandStation->html_background_image ? url('storage/'.optional($brandStation)->html_background_image) : "https://appadmin.radio2go.fm/images/Background_m_logo2.png";


    $data = view('html', compact('brandStation', 'logo', 'background_image', 'audio_url', 'video_url'));
	  
    $jsongFile = $brandStation->name.'.html';
    File::put(public_path('/storage/upload/html/'.$jsongFile), $data);
    return response()->download(public_path('/storage/upload/html/'.$jsongFile));
})->name('create.html');

    //
});





Route::get('/channel/{brandStation}', function (BrandStation $brandStation) {
    
    $name = strtolower(str_replace(' ', '-', $brandStation->name));

    $url = 'https://channel.radio2go.fm/'.$brandStation->id.'/'.$name.'.html';
    return redirect($url);
  // return  redirect(route('open.html.page', ['brandStation' => $brandStation->id, 'name' => $name.'.html']));
  
   
})->name('open.html');

Route::get('/{brandStation}/{name}', function(BrandStation $brandStation) {

   
 if($brandStation->artwork_image){
        $logo = $brandStation->artwork_image;
    } else {
        $logo = $brandStation->html_background_image ? url('storage/'.optional($brandStation)->logo_url): "https://appadmin.radio2go.fm/logo.png";
    }

    if($brandStation->audio_url){
        $audio_url = url('storage/'.$brandStation->audio_url);
    } else {
        $audio_url = $brandStation->stream_url;
    }

    if($brandStation->video_url){
        $video_url = url('storage/'.$brandStation->video_url);
    } else {
        $video_url = null;
    }
    
    $background_image = $brandStation->html_background_image ? url('storage/'.optional($brandStation)->html_background_image) : "https://appadmin.radio2go.fm/images/Background_m_logo2.png";


    return view('html', compact('brandStation', 'logo', 'background_image', 'audio_url', 'video_url'));
})->name('open.html.page');





   