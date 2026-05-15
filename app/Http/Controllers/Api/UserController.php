<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\SectionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function change_password(ChangePasswordRequest $request)
    {
        $current_password = Hash::check($request->actual_password, $request->user()->password);
        if ($current_password == true){
            $new_password = $this->service->changePassword($request->user(), $request->new_password);

            $resource = new UserResource($new_password);

            return $resource->response()->setStatusCode(200);
        }
        else{
            abort(400, 'Bad Request');
        }
    }

    public function updateRole(User $user, Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:owner,employed,client'
        ]);

        if($user->role === $validated['role']){
            abort(400, 'Bad Request');
        }

        $user->update([
            'role' => $validated['role']
        ]);

        return response()->json(['message' => 'Rol actualizado'], 200);
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
