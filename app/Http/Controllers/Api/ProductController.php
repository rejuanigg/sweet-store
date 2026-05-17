<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\ToggleFeaturedProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\ToggleFeatured;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $service
    ){}

    public function featured()
    {
        $products = Product::query()
            ->with([
                'images:id,product_id,image',
                'stocks:id,product_id,quantity',
                'categories:id,name',
            ])
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->take(4)
            ->get();
        return ProductResource::collection($products);
    }

    public function toggleFeatured(ToggleFeaturedProductRequest $request, Product $product)
    {
        $product = $this->service->toggleFeatured($product, $request->validated());

        return response()->json([
            'message' => 'Producto destacado actualizado correctamente.',
            'data' => new ProductResource($product),
        ]);
    }
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function index()
    {
        $myProducts = Product::query()->paginate(20);

        return ProductResource::collection($myProducts);
    }

    public function store(StoreProductRequest $request)
    {
        $newProduct = $this->service->store($request->validated());

        $resource = new ProductResource($newProduct);

        return $resource->response()->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $editProduct = $this->service->update($product ,$request->validated());

        $resource = new ProductResource($editProduct);

        return $resource->response()->setStatusCode(200);
    }

    public function destroy(Product $product)
    {
        $this->service->destroy($product);

        return response()->noContent();
    }
}
