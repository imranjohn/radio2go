<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    {{-- Inertia --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>

    {{-- Ping CRM --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>

    <script src="{{ mix('/js/app.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.12/plyr.css" />
<script src="https://cdn.plyr.io/3.6.12/plyr.js"></script>
    <style>

.wrapper{
  display:grid;
  height:100vh;
  place-items:center
}
    </style>
</head>
<body class="font-sans leading-none text-gray-700 antialiased">

<div class="p-6 min-h-screen flex justify-center items-center" style="background-image: url({{$background_image}})" >
<div >
    <div class="max-w-sm bg-black rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
    <a href="#">
    <img class="p-8 rounded-t-lg" src="{{$logo}}" alt="product image" />
    </a>
    <div class="px-5 pb-5">
        <a href="#">
            <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
            {{$brandStation->name}}</h5>
        </a>
        <div class="flex items-center mt-2.5 mb-5">
           
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 ml-3">
            {{$brandStation->description}}
            </span>
        </div>
        <div class="flex justify-between items-center">
        <audio crossorigin playsinline>                      
  <source src="{{$brandStation->stream_url}}" type="audio/mp3">
   </audio>
        </div>
    </div>
</div>
    <!-- <img src="https://source.unsplash.com/random/350x350" alt=" random imgee" class="w-full object-cover object-center rounded-lg shadow-md">    
    
 <div class="relative px-4 -mt-16  ">
   <div class="bg-black p-6 rounded-lg shadow-lg">
   
    
    <h4 class="mt-1 text-xl font-semibold uppercase leading-tight truncate">{{$brandStation->name}}</h4>
 
  <div class="mt-1">
   {{$brandStation->description}}
  </div>
  <div class="mt-4">
  <audio crossorigin playsinline>                      
  <source src="{{$brandStation->stream_url}}" type="audio/mp3">
   </audio>
  </div>  
  </div>
 </div> -->
  
</div>
  </div>
  <script>
  // Change "{}" to your options:
// https://github.com/sampotts/plyr/#options
const player = new Plyr('audio', {});

// Expose player so it can be used from the console
window.player = player;

</script>
</body>
</html>