<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Jobs\ImportProductsFromCsvJob;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $repo,
        protected ProductService $service
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $perPage = $request->get('per_page', 15);

        if ($user->hasRole('vendor')) {
            $data = $this->repo->paginateForVendor($user->id, $perPage);
        } else {
            $data = $this->repo->paginateAll($perPage);
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'sku' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|integer',
            'initial_quantity' => 'nullable|integer',
            'low_stock_threshold' => 'nullable|integer',
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $vendorId = $user->hasRole('vendor') ? $user->id : null;
        $product = $this->service->createProduct($request->all(), $vendorId);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    // show
    public function show($id)
    {
        // 1. Fetch the Product
        $product = $this->repo->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $user = auth('api')->user();

        // 3. Check if the user is a Vendor AND if they do not own this product.
        if ($user->hasRole('vendor')) {
            // If they are a Vendor, they must own the product (vendor_id must match).
            if ($product->vendor_id !== $user->id) {
                return response()->json([
                    'message' => 'Forbidden. You do not have management access to this product.'
                ], 403);
            }
        }

        // 4.Deny Customer Access Explicitly:
        if ($user->hasRole('customer')) {
            return response()->json([
                'message' => 'Forbidden. Customers cannot access product management details.'
            ], 403);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product)
            return response()->json(['message' => 'Not found'], 404);

        // authorization: vendor only update own product unless admin
        $user = auth('api')->user();
        if ($user->hasRole('vendor') && $product->vendor_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product = $this->service->updateProduct($product, $request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product)
            return response()->json(['message' => 'Not found'], 404);
        $user = auth('api')->user();
        if ($user->hasRole('vendor') && $product->vendor_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $this->repo->delete($product);
        return response()->json(['message' => 'Deleted']);
    }

    // CSV import endpoint (file upload), only vendor/admin
    public function importCsv(Request $request)
    {
        $this->authorizeImport($request);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        $path = $request->file('file')->store('imports');

        $vendorId = auth('api')->user()->hasRole('vendor') ? auth('api')->id() : null;
        ImportProductsFromCsvJob::dispatch(storage_path('app/' . $path), $vendorId);

        return response()->json(['message' => 'Import queued'], 202);
    }

    protected function authorizeImport(Request $request)
    {
        $user = auth('api')->user();
        if (!$user->hasRole('vendor') && !$user->hasRole('admin')) {
            abort(response()->json(['message' => 'Forbidden'], 403));
        }
    }

    // decrease inventory endpoint e.g. when order created
    public function decreaseInventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variant_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);
        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()], 422);

        try {
            $inv = $this->service->decreaseInventory($request->variant_id, $request->quantity);
            return response()->json($inv);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // search
    public function search(Request $request)
    {
        $q = $request->get('q');
        if (!$q)
            return response()->json(['message' => 'Query required'], 422);
        $perPage = $request->get('per_page', 15);
        $results = $this->repo->search($q, $perPage);
        return response()->json($results);
    }
}
