<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Inventory;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;
use App\Events\InventoryLowEvent;
use DB;

class ProductService
{
    protected ProductRepository $repo;

    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    public function createProduct(array $data, $vendorId = null): Product
    {
        return DB::transaction(function () use ($data, $vendorId) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']) . '-' . Str::random(5);
            if ($vendorId)
                $data['vendor_id'] = $vendorId;
            $product = $this->repo->create($data);

            // variants (if provided)
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $vdata) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $vdata['sku'] ?? null,
                        'attributes' => $vdata['attributes'] ?? null,
                        'price' => $vdata['price'] ?? $product->price,
                    ]);
                    Inventory::create([
                        'variant_id' => $variant->id,
                        'quantity' => $vdata['quantity'] ?? 0,
                    ]);
                }
            } else {
                // create a default variant if no variants passed
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku ?? null,
                    'attributes' => null,
                    'price' => $product->price,
                ]);
                Inventory::create([
                    'variant_id' => $variant->id,
                    'quantity' => $data['initial_quantity'] ?? 0,
                ]);
            }

            return $product->fresh('variants.inventory');
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $this->repo->update($product, $data);

            if (isset($data['variants']) && is_array($data['variants'])) {
                // simplistic strategy: upsert variants by sku if present, else create
                foreach ($data['variants'] as $vdata) {
                    if (!empty($vdata['id'])) {
                        $variant = ProductVariant::find($vdata['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $vdata['sku'] ?? $variant->sku,
                                'attributes' => $vdata['attributes'] ?? $variant->attributes,
                                'price' => $vdata['price'] ?? $variant->price,
                            ]);
                            if (isset($vdata['quantity'])) {
                                $inv = $variant->inventory ?? Inventory::create(['variant_id' => $variant->id, 'quantity' => 0]);
                                $inv->quantity = $vdata['quantity'];
                                $inv->save();
                            }
                        }
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $vdata['sku'] ?? null,
                            'attributes' => $vdata['attributes'] ?? null,
                            'price' => $vdata['price'] ?? $product->price,
                        ]);
                        Inventory::create([
                            'variant_id' => $variant->id,
                            'quantity' => $vdata['quantity'] ?? 0,
                        ]);
                    }
                }
            }

            return $product->fresh('variants.inventory');
        });
    }

    public function decreaseInventory(int $variantId, int $qty = 1)
    {
        return DB::transaction(function () use ($variantId, $qty) {
            $inv = Inventory::lockForUpdate()->where('variant_id', $variantId)->firstOrFail();
            if ($inv->quantity < $qty) {
                throw new \Exception('Insufficient stock');
            }
            $inv->quantity -= $qty;
            $inv->save();

            // check low stock
            $product = $inv->variant->product;
            // if total quantity <= threshold dispatch event
            if ($product->total_quantity <= $product->low_stock_threshold) {
                event(new InventoryLowEvent($product));
            }

            return $inv;
        });
    }

    public function increaseInventory(int $variantId, int $qty = 1)
    {
        return DB::transaction(function () use ($variantId, $qty) {
            $inv = Inventory::lockForUpdate()->firstOrCreate(['variant_id' => $variantId], ['quantity' => 0]);
            $inv->quantity += $qty;
            $inv->save();
            return $inv;
        });
    }
}
