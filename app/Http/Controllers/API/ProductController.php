<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Services\SystemNotificationService;

class ProductController extends Controller
{
    public function __construct(
        private readonly SystemNotificationService $notificationService
    ) {
    }

    public function index()
    {
        $products = Product::all();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'brand_id' => [
                'required',
                Rule::exists('brands', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'packaging_type_id' => 'required|exists:packaging_types,id',
            'packaging_size_id' => 'required|exists:packaging_sizes,id',
            'product_name' => 'required|string|max:255',
            'image_url' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image_url = null;
        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('products', 'public');
        }

        $product = Product::create([
            'user_id' => auth()->id(),
            'category_id' => $validator['category_id'],
            'brand_id' => $validator['brand_id'],
            'packaging_type_id' => $validator['packaging_type_id'],
            'packaging_size_id' => $validator['packaging_size_id'],
            'product_name' => $validator['product_name'],
            'image_url' => $image_url,
        ]);

        $this->notificationService->notifyActiveUsers(
            title: 'Produk baru ditambahkan',
            message: "Produk {$product->product_name} berhasil dibuat.",
            type: 'success',
            actionUrl: route('dashboard.products.show', ['id' => $product->id]),
            meta: [
                'product_id' => $product->id,
                'event' => 'created',
            ]
        );

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Produk berhasil ditambahkan.',
        ], 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => [
                'sometimes',
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'brand_id' => [
                'sometimes',
                'required',
                Rule::exists('brands', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'packaging_type_id' => 'sometimes|required|exists:packaging_types,id',
            'packaging_size_id' => 'sometimes|required|exists:packaging_sizes,id',
            'product_name' => 'sometimes|required|string|max:255',
            'image_url' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('products', 'public');
            $validated['image_url'] = $image_url;
        } else {
            unset($validated['image_url']);
        }

        $product->fill($validated);
        $product->save();

        $this->notificationService->notifyActiveUsers(
            title: 'Produk diperbarui',
            message: "Produk {$product->product_name} berhasil diperbarui.",
            type: 'info',
            actionUrl: route('dashboard.products.show', ['id' => $product->id]),
            meta: [
                'product_id' => $product->id,
                'event' => 'updated',
            ]
        );

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Produk berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->product_name;
        $productId = $product->id;
        $product->delete();

        $this->notificationService->notifyActiveUsers(
            title: 'Produk dihapus',
            message: "Produk {$productName} telah dihapus.",
            type: 'warning',
            actionUrl: null,
            meta: [
                'product_id' => $productId,
                'event' => 'deleted',
            ]
        );

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}

