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
            return Response::json(['error' => 'Invalid credentials'], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        if (Auth::user()->tokens()->count() > 0) {
            return Response::json(['error' => 'User already has tokens created. Call /logout first.'], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = User::query()->where('email', $request->get('email'))->firstOrFail();
        $token = $user->createToken($user->name, $user->abilities ?? [], Carbon::now()->addDays(7));

        return Response::json($token);
    }

    public function logout(): \Illuminate\Http\Response
    {
        Auth::user()->tokens()->delete();

        return Response::noContent();
    }
}
