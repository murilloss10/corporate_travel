<?php

namespace App\Jobs;

use App\Models\TravelOrder;
use App\Notifications\TravelOrderDisapproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class DeletingTravelOrderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $travel_order_id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $travelOrder = TravelOrder::withTrashed()->with('user:id,name,email')->find($this->travel_order_id);

        $travelOrder->status = 'Cancelado';
        $travelOrder->save();

        $travelOrder->departureDate = $travelOrder->departure_date->format('d/m/Y');
        $travelOrder->returnDate = $travelOrder->return_date->format('d/m/Y');

        Notification::send($travelOrder->user, new TravelOrderDisapproval($travelOrder->toArray()));
    }
}
