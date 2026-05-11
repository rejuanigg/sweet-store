<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\SectionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $service
    )
    {
    }

    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    public function register(StoreUserRequest $request)
    {

        $newUser = $this->service->store($request->validated());

        $resource = new UserResource($newUser);

        return $resource->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request)
    {
        $editUser = $this->service->update($request->user(), $request->validated());

        $resource = new UserResource($editUser);

        return $resource->response()->setStatusCode(200);
    }

    public function destroy(User $user)
    {
        $this->service->destroy($user);

        return response()->noContent();
    }
}
