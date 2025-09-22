<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ✅ Return only products created or synced for this user
        $products = Product::with('integration')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'integration_id'       => 'nullable|numeric|exists:integrations,id',
            'external_product_id'  => 'nullable|string',
            'name'                 => 'required|string',
            'sku'                  => 'nullable|string',
            'price'                => 'required|numeric',
            'stock'                => 'required|integer',
            'status'               => 'nullable|in:active,inactive,archived',
            'platform'             => 'nullable|string',
        ]);

        $user = auth()->user();

        // ✅ If integration is provided, check ownership
        if (isset($validated['integration_id'])) {
            $integration = Integration::where('id', $validated['integration_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$integration) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not allowed to add products to this integration'
                ], 403);
            }
        }
        
        try {
            $product = Product::create([
                'user_id'             => $user->id,
                'integration_id'      => $validated['integration_id'] ?? null,
                'external_product_id' => $validated['external_product_id'] ?? null,
                'name'                => $validated['name'],
                'sku'                 => $validated['sku'] ?? null,
                'price'               => $validated['price'],
                'stock'               => $validated['stock'],
                'status'              => $validated['status'] ?? 'active',
                'platform'            => $validated['platform'] ?? null,
                'source'              => 'manual', // ✅ Mark as manual
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data'    => $product
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ProductController.store failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json(['message' => 'Failed to create product'], 500);
        }
    }

    public function show(Product $product)
    {
        $user = auth()->user();

        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $user = auth()->user();

        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name'   => 'sometimes|string',
            'sku'    => 'nullable|string',
            'price'  => 'sometimes|numeric',
            'stock'  => 'sometimes|integer',
            'status' => 'nullable|in:active,inactive,archived',
        ]);

        try {
            $product->update($validated);

            return response()->json($product);
        } catch (\Throwable $e) {
            Log::error('ProductController.update failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);
            return response()->json(['message' => 'Failed to update product'], 500);
        }
    }

    public function destroy(Product $product)
    {
        $user = auth()->user();

        if ($product->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $product->delete();
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            Log::error('ProductController.destroy failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
    }
}
