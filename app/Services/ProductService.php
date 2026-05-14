<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;

class ProductService
{
    public function store(array $data)
    {
        $categories = Arr::pull($data, 'categories');
        $product = Product::create($data);
        $product->categories()->sync($categories);

        return $product;
    }

    public function toggleFeatured(Product $product, array $data)
    {
        if ($data['is_featured'] === true) {
            $featuredCount = Product::where('is_featured', true)->count();

            if (!$product->is_featured && $featuredCount >= 4) {
                abort(422, 'Ya hay 4 productos destacados.');
            }

            if (!$product->is_featured) {
                $nextOrder = Product::where('is_featured', true)->max('featured_order') + 1;

                $product->update([
                    'is_featured' => true,
                    'featured_order' => $nextOrder,
                ]);
            }
        }

        if ($data['is_featured'] === false) {
            $oldOrder = $product->featured_order;

            $product->update([
                'is_featured' => false,
                'featured_order' => null,
            ]);

            if ($oldOrder !== null) {
                Product::where('is_featured', true)
                    ->where('featured_order', '>', $oldOrder)
                    ->decrement('featured_order');
            }
        }

        return $product->fresh(['images', 'stocks', 'categories']);
    }

    
    public function update(Product $product, array $data)
    {
        $categories = Arr::pull($data, 'categories');
        $product->categories()->sync($categories);
        $product->update($data);
        return $product;
    }

    public function destroy(Product $product)
    {
        return $product->delete();
    }

}

?>
