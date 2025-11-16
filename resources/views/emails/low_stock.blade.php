@component('mail::message')
# Low stock alert

Product: **{{ $product->name }}**
Total Quantity: **{{ $product->total_quantity }}**
Threshold: **{{ $product->low_stock_threshold }}

@component('mail::button', ['url' => config('app.url') . '/admin/products/' . $product->id])
Manage product
@endcomponent

Thanks,<br />{{ config('app.name') }}
@endcomponent
