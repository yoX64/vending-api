<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionBuyRequest;
use App\Http\Requests\TransactionDepositRequest;
use App\Models\Product;
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

    public function buy(TransactionBuyRequest $request): \Illuminate\Http\Response|JsonResponse
    {
        if (!in_array(AuthServiceProvider::ABILITY_BUY, Auth::user()->abilities ?? [])) {
            return Response::json(['error' => 'You are not allowed to buy'],
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        }

        /** @var Product $product */
        $product = Product::query()->findOrFail($request->get('product_id'));

        if ($product->amount_available < $request->get('amount')) {
            return Response::json(['error' => 'Not enough products available'],
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        $totalCost = $request->get('amount') * $product->cost;

        if (Auth::user()->deposit < $totalCost) {
            return Response::json(['error' => 'Not enough money'],
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = Auth::user();
            $user->deposit -= $totalCost;
            $user->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()],
                \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json([
            'product_purchased' => $product->name,
            'total_spent' => $totalCost,
            'total_remaining' => $user->deposit,
        ]);
    }

    public function reset(): \Illuminate\Http\Response|JsonResponse
    {
        if (!in_array(AuthServiceProvider::ABILITY_BUY, Auth::user()->abilities ?? [])) {
            return Response::json(['error' => 'You are not allowed to reset'],
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        }

        try {
            $user = Auth::user();
            $user->deposit = 0;
            $user->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()],
                \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::noContent();
    }
}
