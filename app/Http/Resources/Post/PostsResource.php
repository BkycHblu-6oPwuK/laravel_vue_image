<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Images\ImagesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        foreach($this->resource as $key => $post){
            $data[] = $post;
            $data[$key]['images'] = ImagesResource::collection($post->images);
        };
        return $data;
    }
}
