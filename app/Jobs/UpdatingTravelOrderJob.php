<?php

namespace App\Jobs;

use App\Models\TravelOrder;
use App\Notifications\TravelOrderApproval;
use App\Notifications\TravelOrderDisapproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class UpdatingTravelOrderJob implements ShouldQueue
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
        $travelOrder = TravelOrder::with('user:id,name,email')->find($this->travel_order_id);
        $travelOrder->departureDate = $travelOrder->departure_date->format('d/m/Y');
        $travelOrder->returnDate = $travelOrder->return_date->format('d/m/Y');

        if ($travelOrder && $travelOrder->status === 'Aprovado')
            Notification::send($travelOrder->user, new TravelOrderApproval($travelOrder->toArray()));
        
        if ($travelOrder && $travelOrder->status === 'Cancelado')
            Notification::send($travelOrder->user, new TravelOrderDisapproval($travelOrder->toArray()));
    }
}
