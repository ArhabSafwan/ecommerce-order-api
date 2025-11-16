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

    public function search(string $q, int $perPage = 15)
    {
        // Full-text search using MATCH ... AGAINST for MySQL
        $query = Product::query()
            ->selectRaw('products.*, MATCH(name, description, sku) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance', [$q])
            ->whereRaw('MATCH(name, description, sku) AGAINST(? IN NATURAL LANGUAGE MODE)', [$q])
            ->orderByDesc('relevance');

        return $query->with('variants.inventory')->paginate($perPage);
    }
}
