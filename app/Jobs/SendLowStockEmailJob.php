<?php
namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockMail;

class SendLowStockEmailJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        // choose recipients: vendor and admin(s)
        $vendor = $this->product->vendor;
        $admins = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

        $recipients = $admins->pluck('email')->filter()->toArray();
        if ($vendor && $vendor->email)
            $recipients[] = $vendor->email;

        foreach (array_unique($recipients) as $email) {
            Mail::to($email)->queue(new LowStockMail($this->product));
        }
    }
}
