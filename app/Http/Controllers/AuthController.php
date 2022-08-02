<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }

        /** @var User $user */
        $user = User::query()->where('email', $request->get('email'))->firstOrFail();
        $token = $user->createToken($user->name, json_decode($user->abilities), Carbon::now()->addDays(7));

        return Response::json($token);
    }
}
