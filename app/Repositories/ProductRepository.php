<?php
namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::where('vendor_id', $vendorId)->with('variants.inventory')->paginate($perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('variants.inventory')->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return Product::with('variants.inventory')->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    // Natural Language Mode
    // public function search(string $q, int $perPage = 15)
    // {
    //     // Full-text search using MATCH ... AGAINST for MySQL
    //     $query = Product::query()
    //         ->selectRaw('products.*, MATCH(name, description, sku) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance', [$q])
    //         ->whereRaw('MATCH(name, description, sku) AGAINST(? IN NATURAL LANGUAGE MODE)', [$q])
    //         ->orderByDesc('relevance');

    //     return $query->with('variants.inventory')->paginate($perPage);
    // }

    // Boolean Mode Search
    public function search(string $q, int $perPage = 15)
    {
        $booleanQuery = '+' . $q . '*';
        $fields = 'name, description, sku';
        $query = Product::query()
            // Select relevance score
            ->selectRaw("products.*, MATCH({$fields}) AGAINST(? IN BOOLEAN MODE) AS relevance", [$booleanQuery])

            // Filter: only include products that match the boolean expression
            ->whereRaw("MATCH({$fields}) AGAINST(? IN BOOLEAN MODE)", [$booleanQuery])

            // Sort by relevance score (best matches first)
            ->orderByDesc('relevance');
        return $query->with('variants.inventory')->paginate($perPage);
    }
}
