<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreResource;
use App\Http\Requests\UsersUpdateResource;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return Response::json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsersStoreResource $request): JsonResponse
    {
        try {
            $user = new User;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->abilities = $request->get('abilities');
            $user->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        return Response::json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsersUpdateResource $request, $id): JsonResponse
    {
        try {
            /** @var User $user */
            $user = User::query()->findOrFail($id);
            $user->name = $request->get('name', $user->name);
            $user->email = $request->get('email', $user->email);

            if ($request->has('password')) {
                $user->password = Hash::make($request->get('password'));
            }

            if ($request->has('abilities')) {
                $user->abilities = $request->get('abilities');
            }

            $user->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): \Illuminate\Http\Response|JsonResponse
    {
        try {
            $user = User::query()->findOrFail($id);
            $user->delete();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return Response::noContent();
    }
}
