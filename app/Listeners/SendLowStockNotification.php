<?php

namespace App\Listeners;

use App\Events\InventoryLowEvent;
use App\Jobs\SendLowStockEmailJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLowStockNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InventoryLowEvent $event): void
    {
        // Dispatch a queued job that emails admins/vendors
        SendLowStockEmailJob::dispatch($event->product);
    }
}
