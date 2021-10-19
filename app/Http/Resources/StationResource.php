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
        return [
            'name' => $this->name,
            'streamURL' => $this->stream_url,
            'imageURL' => $this->image_url,
            'artworkImage' => $this->artwork_image,
            'desc' => $this->description,
            'longDesc' => $this->long_description,
        ];

        
        //return parent::toArray($request);
    }
}
