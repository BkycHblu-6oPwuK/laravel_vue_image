<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function __invoke (UpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        $images = isset($data['images']) ? $data['images'] : null;
        $imageIdsForDelete = isset($data['image_ids_for_delete']) ? $data['image_ids_for_delete'] : null;
        $imageURLsForDelete = isset($data['image_urls_for_delete']) ? $data['image_urls_for_delete'] : null;
        unset($data['images'],$data['image_ids_for_delete'],$data['image_urls_for_delete']);
        $currentImages = $post->images;
        if($imageIdsForDelete){
            foreach($currentImages as $currentImage){
                if(in_array($currentImage->id,$imageIdsForDelete)){
                    Storage::disk('public')->delete($currentImage->path);
                    Storage::disk('public')->delete(str_replace('images/','images/prev_',$currentImage->path));
                    $currentImage->delete();
                }
            }
        }
        if($imageURLsForDelete){
            foreach($imageURLsForDelete as $imageURLForDelete){
                $removeStr = $request->root() . '/storage/';
                $path = str_replace($removeStr,'',$imageURLForDelete);
                Storage::disk('public')->delete($path);
            }
        }
        if($images){
            foreach($images as $image){
                $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
                $previewName = 'prev_' . $name;
                $filePath = Storage::disk('public')->putFileAs('/images',$image,$name);
                Image::create([
                    'post_id' => $post->id,
                    'path' => $filePath,
                    'url' => url('/storage/' . $filePath),
                    'preview_url' => url('/storage/images/' . $previewName)
                ]);
                \Intervention\Image\Facades\Image::make($image)->fit(100,100)
                ->save(storage_path('app/public/images/' . $previewName));
            }
        }
        $post->update($data);
        return response()->json(['message' => 'success']);
    }
}
