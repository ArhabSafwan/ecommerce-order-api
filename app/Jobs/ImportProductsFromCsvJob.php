<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;
use Illuminate\Support\Str;
use App\Services\ProductService;

class ImportProductsFromCsvJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $path;
    protected $vendorId;
    public function __construct(string $path, ?int $vendorId = null)
    {
        $this->path = $path;
        $this->vendorId = $vendorId;
    }

    public function handle(ProductService $service)
    {
        $csv = Reader::createFromPath($this->path, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $row) {
            // Expected columns: name, sku, description, price, initial_quantity, low_stock_threshold, attributes_json (optional)
            $data = [
                'name' => $row['name'] ?? 'Untitled',
                'sku' => $row['sku'] ?? null,
                'description' => $row['description'] ?? null,
                'price' => $row['price'] ?? 0,
                'initial_quantity' => isset($row['initial_quantity']) ? (int) $row['initial_quantity'] : 0,
                'low_stock_threshold' => isset($row['low_stock_threshold']) ? (int) $row['low_stock_threshold'] : 5,
            ];

            // if attributes_json present, treat as variant
            if (!empty($row['attributes_json'])) {
                $attributes = json_decode($row['attributes_json'], true);
                $data['variants'] = [
                    [
                        'sku' => $row['sku'] ?? null,
                        'attributes' => $attributes,
                        'price' => $row['price'] ?? null,
                        'quantity' => $row['initial_quantity'] ?? 0,
                    ]
                ];
            }

            $service->createProduct($data, $this->vendorId);
        }
    }
}
