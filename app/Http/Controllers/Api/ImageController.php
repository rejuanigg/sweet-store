<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Services\ImageService;
use Cloudinary\Cloudinary;

class ImageController extends Controller
{
    private \Cloudinary\Cloudinary $cloudinary;
    public function __construct(
        private ImageService $service
    ){

        $this->cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],
    'url' => [
        'secure' => true,
    ],
        ]);
    }

    public function index()
    {
        $myImages = Image::all();

        return ImageResource::collection($myImages);
    }

    public function store(StoreImageRequest $request,)
    {

        $result = $this->cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath()
        );

        $path = $result['secure_url'];
        $publicId = $result['public_id'];

        $data = array_merge($request->validated(), [
            'image' => $path,
            'public_id' => $publicId
        ]);

        $newImage = $this->service->store($data);

        $resource = new ImageResource($newImage);

        return $resource->response()->setStatusCode(201);
    }

    public function update(UpdateImageRequest $request, Image $image)
    {


        $result = $this->cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath()
        );

        $path = $result['secure_url'];
        $publicId = $result['public_id'];

        $data = array_merge($request->validated(), [
            'image' => $path,
            'public_id' => $publicId
        ]);

        $editImage = $this->service->update($image , $data);

        $resource = new ImageResource($editImage);

        return $resource->response()->setStatusCode(200);
    }

    public function destroy(Image $image)
    {
        if($image->public_id){
            $this->cloudinary->uploadApi()->destroy($image->public_id);
        }

        $this->service->destroy($image);

        return response()->noContent();
    }
}
