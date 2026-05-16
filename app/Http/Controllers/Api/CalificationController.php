<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalificationRequest;
use App\Http\Requests\UpdateCalificationRequest;
use App\Http\Resources\CalificationResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Calification;
use App\Models\Product;
use App\Services\CalificationService;
use Illuminate\Support\Facades\Auth;

class CalificationController extends Controller
{
    public function __construct(
        private CalificationService $service
    ){}

    public function index(Product $product)
    {
        $calificationsProducts = $product->califications;

        return CalificationResource::collection($calificationsProducts);
    }

    public function show(Calification $calification)
    {
        $authUser = Auth::user();

        $access = in_array($authUser->role, ['employed', 'owner']);

        $accessUser = $calification->user_id === $authUser->id;

        if ($access || $accessUser)
            {
                return new CalificationResource($calification);
            }
        else
            {
                abort(403, 'No tenés permiso para ver esto.');
            }
    }

    public function store(StoreCalificationRequest $request)
    {
        $newCalification = $this->service->store($request->user() ,$request->validated());

        $resource = new CalificationResource($newCalification);

        return $resource->response()->setStatusCode(201);
    }

    public function update(Calification $calification, UpdateCalificationRequest $request)
    {
        abort_if($calification->user_id !== Auth::id(), 403);

        $editCalification = $this->service->update($calification, $request->validated());

        $resource = new CalificationResource($editCalification);

        return $resource->response()->setStatusCode(200);
    }

    public function destroy(Calification $calification)
    {
        abort_if($calification->user_id !== Auth::id(), 403);

        $this->service->destroy($calification);

        return response()->noContent();
    }
}
