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
         
      $audio_link = isset($this->audio_url) && file_exists('storage/'.$this->audio_url) ? url('storage/'.$this->audio_url) : null;
    
        return [
            'id' => $this->id,
            'name' => $this->name,
            'streamURL' => $this->stream_url,
            'imageURL' => $this->image_url,
            'artworkImage' => $this->artwork_image,
            'desc' => $this->description,
            'longDesc' => $this->long_description,
            'audio_url' => $audio_link,
            'isFavorite' => $request->isFavorite ? true : false
        ];

        
        //return parent::toArray($request);
    }
}
