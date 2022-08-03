<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionDepositRequest;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
    public function deposit(TransactionDepositRequest $request): \Illuminate\Http\Response|JsonResponse
    {
        if (!in_array(AuthServiceProvider::ABILITY_BUY, Auth::user()->abilities ?? [])) {
            return Response::json(['error' => 'You are not allowed to deposit'], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        }

        try {
            $user = Auth::user();
            $user->deposit += $request->get('amount');
            $user->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return Response::noContent();
    }
}
