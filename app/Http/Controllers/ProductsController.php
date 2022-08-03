<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductsStoreRequest;
use App\Http\Requests\ProductsUpdateRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\UnauthorizedException;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        return Response::json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductsStoreRequest $request): JsonResponse
    {
        try {
            $product = new Product;
            $product->seller_id = Auth::id();
            $product->name = $request->get('name');
            $product->cost = $request->get('cost');
            $product->amount_available = $request->get('amount_available');
            $product->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($product);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);

        return Response::json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductsUpdateRequest $request, $id): jsonResponse
    {
        try {
            /** @var Product $product */
            $product = Product::query()->findOrFail($id);

            if ($product->seller_id !== Auth::id()) {
                throw new UnauthorizedException('You are not authorized to edit this product');
            }

            $product->name = $request->get('name', $product->name);
            $product->cost = $request->get('cost', $product->cost);
            $product->amount_available = $request->get('amount_available', $product->amount_available);
            $product->save();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): \Illuminate\Http\Response|JsonResponse
    {
        try {
            /** @var Product $product */
            $product = Product::query()->findOrFail($id);

            if ($product->seller_id !== Auth::id()) {
                throw new UnauthorizedException('You are not authorized to delete this product');
            }

            $product->delete();
        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::noContent();
    }
}
