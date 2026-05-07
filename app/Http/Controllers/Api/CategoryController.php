<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{

    public function __construct(
        private CategoryService $service
    ){}

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function index()
    {
        $myCategories = Category::all();

        return CategoryResource::collection($myCategories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $createCategory = $this->service->store($request->validated());

        $resource = new CategoryResource($createCategory);

        return $resource->response()->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {

        $editCategory = $this->service->update($category, $request->validated());

        $resource = new CategoryResource($editCategory);

        return $resource->response()->setStatusCode(200);
    }

    public function destroy(Category $category)
    {
        $this->service->destroy($category);

        return response()->noContent();
    }
}
