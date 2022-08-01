<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }

        /** @var User $user */
        $user = User::query()->where('email', $request->get('email'))->firstOrFail();

        return $user->createToken($user->name)->plainTextToken;
    }
}
