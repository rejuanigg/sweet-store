<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('auth_token');
            $role = $request->user()->role;
            $id = $request->user()->id;
            return ['token'=>$token->plainTextToken, 'role'=> $role, 'id'=>$id];
        }
        else {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $resource = new UserResource($user);
        return $resource->response()->setStatusCode(200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->noContent();
    }
}
