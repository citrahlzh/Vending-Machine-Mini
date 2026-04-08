<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\PackagingType;
use App\Models\PackagingSize;
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
            'packaging_type_id' => 'required_without:packaging_type_new|nullable|exists:packaging_types,id',
            'packaging_type_new' => 'nullable|string|max:255',
            'packaging_size_id' => 'required_without:packaging_size_new|nullable|exists:packaging_sizes,id',
            'packaging_size_new' => 'nullable|string|max:255',
            'product_name' => 'required|string|max:255',
            'image_url' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $packagingTypeId = $validator['packaging_type_id'] ?? null;
        $packagingSizeId = $validator['packaging_size_id'] ?? null;

        $packagingTypeNew = isset($validator['packaging_type_new']) ? trim($validator['packaging_type_new']) : '';
        if ($packagingTypeNew !== '') {
            $packagingType = PackagingType::firstOrCreate(
                ['packaging_type' => $packagingTypeNew],
                ['user_id' => $request->user()->id]
            );
            $packagingTypeId = $packagingType->id;
        }

        $packagingSizeNew = isset($validator['packaging_size_new']) ? trim($validator['packaging_size_new']) : '';
        if ($packagingSizeNew !== '') {
            $packagingSize = PackagingSize::firstOrCreate(
                ['size' => $packagingSizeNew],
                ['user_id' => $request->user()->id]
            );
            $packagingSizeId = $packagingSize->id;
        }

        $image_url = null;
        if ($request->hasFile('image_url')) {
            $image_url = $request->file('image_url')->store('products', 'public');
        }

        $product = Product::create([
            'user_id' => $request->user()->id,
            'category_id' => $validator['category_id'],
            'brand_id' => $validator['brand_id'],
            'packaging_type_id' => $packagingTypeId,
            'packaging_size_id' => $packagingSizeId,
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
            'packaging_type_id' => 'sometimes|nullable|exists:packaging_types,id',
            'packaging_type_new' => 'nullable|string|max:255',
            'packaging_size_id' => 'sometimes|nullable|exists:packaging_sizes,id',
            'packaging_size_new' => 'nullable|string|max:255',
            'product_name' => 'sometimes|required|string|max:255',
            'image_url' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        $packagingTypeNew = isset($validated['packaging_type_new']) ? trim($validated['packaging_type_new']) : '';
        if ($packagingTypeNew !== '') {
            $packagingType = PackagingType::firstOrCreate(
                ['packaging_type' => $packagingTypeNew],
                ['user_id' => $request->user()->id]
            );
            $validated['packaging_type_id'] = $packagingType->id;
        }

        $packagingSizeNew = isset($validated['packaging_size_new']) ? trim($validated['packaging_size_new']) : '';
        if ($packagingSizeNew !== '') {
            $packagingSize = PackagingSize::firstOrCreate(
                ['size' => $packagingSizeNew],
                ['user_id' => $request->user()->id]
            );
            $validated['packaging_size_id'] = $packagingSize->id;
        }

        unset($validated['packaging_type_new'], $validated['packaging_size_new']);

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

