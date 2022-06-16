<!DOCTYPE html>
<html class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ $logo }}">

    {{-- Inertia --}}
    <script
        src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign"
        defer></script>

    {{-- Ping CRM --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>

    <script src="{{ url(mix('/js/app.js')) }}" defer></script>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.12/plyr.css" />
    <script src="https://cdn.plyr.io/3.6.12/plyr.js"></script>
    <style>
        .wrapper {
            display: grid;
            height: 100vh;
            place-items: center
        }

        #myVideo {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: 0;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/react/15.4.2/react.min.js"></script>
    <script src="https://cdn.jsdelivr.net/react/15.4.2/react-dom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/graphiql@0.11.11/graphiql.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/graphql-transport-ws@0.8.3/browser/client.js"></script>
</head>

<body class="font-sans leading-none text-gray-700 antialiased">

    @if ($audio_url)
        <video autoplay loop muted id="myVideo">
            <source src="{{ $audio_url }}" type="video/mp4" class="min-w-full min-h-full absolute object-cover" />
            <source src="{{ $audio_url }}" type="video/ogg" class="min-w-full min-h-full absolute object-cover" />
            <source src="{{ $audio_url }}" type="video/webm" class="min-w-full min-h-full absolute object-cover" />
            Your browser does not support the video tag.
        </video>
    @endif
    <img class="top-0 right-0 absolute object-cover mt-6 mr-6 h-20 w-50 " src="{{ $logo }}">
    <div class="p-6 min-h-screen flex bg-cover bg-center justify-center items-center"
        style="background-image: url('{{ $background_image }}'); background-size: cover; background-position: center">

        <div class="z-50">

            <div class="max-w-sm bg-black rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 	">
                <section class="hero container max-w-screen-lg mx-auto pb-10">
                    <!-- <img class="mx-auto" src="https://picsum.photos/id/1/200/300" alt="screenshot" > -->
                    <img class="p-8 rounded-t-lg mx-auto	" id="background-image" src="{{ $logo }}"
                        alt="product image" />

                </section>
                <!-- <img class="p-8 rounded-t-lg flex items-center justify-center	" src="{{ $logo }}" alt="product image" /> -->

                <div class="px-5 pb-5">
                    <a href="#">
                        <h5 class="text-xl font-semibold tracking-tight text-white" id="radio-title">
                            {{ $brandStation->name }}</h5>
                    </a>
                    <div class="flex items-center mt-2.5 mb-5">

                        <span
                            class="bg-white-100 text-white text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-white-200 dark:text-blue-800 ml-3"
                            id="radio-description">
                            {{ $brandStation->description }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <audio crossorigin playsinline>
                            <!-- <source src="{{ $brandStation->stream_url }}" type="audio/mp3"> -->
                            <source type="audio/mp3">
                        </audio>
                    </div>
                </div>
            </div>
            <!-- <img src="https://source.unsplash.com/random/350x350" alt=" random imgee" class="w-full object-cover object-center rounded-lg shadow-md">
    
 <div class="relative px-4 -mt-16  ">
   <div class="bg-black p-6 rounded-lg shadow-lg">
   
    
    <h4 class="mt-1 text-xl font-semibold uppercase leading-tight truncate">{{ $brandStation->name }}</h4>
 
  <div class="mt-1">
   {{ $brandStation->description }}
  </div>
  <div class="mt-4">
  <audio crossorigin playsinline>
  <source src="{{ $brandStation->stream_url }}" type="audio/mp3">
   </audio>
  </div>
  </div>
 </div> -->

        </div>
    </div>
    <script>
        // Change "{}" to your options:
        // https://github.com/sampotts/plyr/#options
        const player = new Plyr('audio', {
            settings: []
        });

        player.source = {
            type: 'audio',
            title: 'Example title',
            sources: [{
                src: "{{ $audio_url }}",
                type: 'audio/mp3',
            }, ],
        };

        player.on('ended', (event) => {


            player.source = {
                type: 'audio',
                title: 'Example title',
                sources: [{
                    src: "{{ $brandStation->stream_url }}",
                    type: 'audio/mp3',
                }, ],
            };

            player.play();
        });

        // Expose player so it can be used from the console
        window.player = player;
    </script>

    <script>
        async function getData() {

            const data = JSON.stringify({
                query: `query {
                channel(id: "621899fb16dbdb001a9c99d1") {
                id
                name
                playingnow {
                  current {
                    start_time
                    duration_ms
                    metadata {
                      artist
                      title
                    }
                    artwork {
                      url
                    }
                  }
                }
              }
            }`,
            });

            const response = await fetch(
                'https://einsam.playlist-api.deliver.media/graphql', {
                    method: 'post',
                    body: data,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                }
            );

            const json = await response.json();
            const urlLink = json.data.channel.playingnow.current;

            if (urlLink.metadata.title) {
                document.getElementById("radio-title").innerHTML = urlLink.metadata.title;
            }

            if (urlLink.metadata.artist) {
                document.getElementById("radio-description").innerHTML = urlLink.metadata.artist;
            }

            if (urlLink.artwork[0] !== undefined) {
                console.log(urlLink);
                const newLink = urlLink.artwork[0].url;
                document.getElementById("background-image").src = newLink;
            }
        }

        getData();

        const GRAPHQL_ENDPOINT =
            (location.protocol === "https" ? "wss" : "ws") +
            "://einsam.playlist-api.deliver.media/graphql"
        let subClient = new window.SubscriptionsTransportWs.SubscriptionClient(
            GRAPHQL_ENDPOINT, {
                reconnect: true
            },
        )
        subFetcher = subClient.request.bind(subClient)


        subClient.subscribe({
            query: `subscription{songUpdate(channelId:  "621899fb16dbdb001a9c99d1"){    
            channel{
              playingnow {
            current {
              metadata{
                artist
                title
              }
              artwork {
                url
              }
            }
          }    
      }}}`
        }, function(error, data) {
            const songObject = data;

            console.log("========================");
            console.log(songObject);

            if (songObject.songUpdate.channel.playingnow.current.metadata.title) {
                document.getElementById("radio-title").innerHTML = songObject.songUpdate.channel.playingnow.current.metadata.title;
            }

            if (songObject.songUpdate.channel.playingnow.current.metadata.artist) {
                document.getElementById("radio-description").innerHTML = songObject.songUpdate.channel.playingnow.current.metadata.artist;
            }

            if (songObject.songUpdate.channel.playingnow.current.artwork[0] !== undefined) {
                const newLink = songObject.songUpdate.channel.playingnow.current.artwork[0].url;
                document.getElementById("background-image").src = newLink;
            }
        })
    </script>

</body>

</html>
