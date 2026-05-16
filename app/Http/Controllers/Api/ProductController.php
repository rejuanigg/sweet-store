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
        //Creamos dentro de productos a featured, lo que hacemos es usar query para introducir dentro del modelo los datos
        $products = Product::query()
        //Usamos with para evitar consultas extras
            ->with([
                'images:id,product_id,image',
                'stocks:id,product_id,quantity',
                'categories:id,name',
            ])
            //Lo que buscamos aca es traer a las que tengan el valor de featured como true
            ->where('is_featured', true)
            //Las ordena segun el orden de las ft
            ->orderBy('featured_order')
            ///Toma 4
            ->take(4)
            ->get();
        //Devuelve
        return response()->json([
            'data' => $products,
        ]);
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
