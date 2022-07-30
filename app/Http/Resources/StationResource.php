<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         
      $audio_link = isset(optional($this->station)->audio_url) && file_exists('storage/'.optional($this->station)->audio_url) ? url('storage/'.optional($this->station)->audio_url) : null;
    
      if($request->isFavorite){
        $audio_link = isset(optional($this)->audio_url) && file_exists('storage/'.optional($this)->audio_url) ? url('storage/'.optional($this)->audio_url) : null;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'graphql_id' => $this->graphql_id,
            'streamURL' => $this->stream_url,
            'imageURL' => $this->image_url,
            'artworkImage' => $this->artwork_image,
            'desc' => $this->description,
            'longDesc' => $this->long_description,
            'audio_url' => $audio_link,
            'audio_duration' => $this->audio_duration,
            'isFavorite' => $this->is_branded_station ? true : false,
          //  'sorted' => $this->sorted_number
        ]; 
    }
        if(!optional($this->station)->deleted_at){
            return [
                'id' => optional($this->station)->id,
                'name' => optional($this->station)->name,
                'graphql_id' => optional($this->station)->graphql_id,
                'streamURL' => optional($this->station)->stream_url,
                'imageURL' => optional($this->station)->image_url,
                'artworkImage' => optional($this->station)->artwork_image,
                'desc' => optional($this->station)->description,
                'longDesc' => optional($this->station)->long_description,
                'audio_url' => $audio_link,
                'audio_duration' => optional($this->station)->audio_duration,
                'isFavorite' => optional($this->station)->is_branded_station ? true : false,
             //  'sorted' => $this->sorted_number,
            ];
        }
        

        
        //return parent::toArray($request);
    }
}
