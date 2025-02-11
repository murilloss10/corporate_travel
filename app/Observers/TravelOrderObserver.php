<?php

namespace App\Observers;

use App\Jobs\DeletingTravelOrderJob;
use App\Jobs\UpdatingTravelOrderJob;
use App\Models\TravelOrder;

class TravelOrderObserver
{
    /**
     * Handle the TravelOrder "created" event.
     */
    public function created(TravelOrder $travelOrder): void
    {
        //
    }

    /**
     * Handle the TravelOrder "updated" event.
     */
    public function updated(TravelOrder $travelOrder): void
    {
        UpdatingTravelOrderJob::dispatch($travelOrder->id);
    }

    /**
     * Handle the TravelOrder "deleted" event.
     */
    public function deleted(TravelOrder $travelOrder): void
    {
        !app()->environment('testing') && DeletingTravelOrderJob::dispatch($travelOrder->id);
    }

    /**
     * Handle the TravelOrder "restored" event.
     */
    public function restored(TravelOrder $travelOrder): void
    {
        //
    }

    /**
     * Handle the TravelOrder "force deleted" event.
     */
    public function forceDeleted(TravelOrder $travelOrder): void
    {
        //
    }
}
