<?php

namespace App\Observers;

use App\Models\TravelOrder;

class CancelTravelOrderObserver
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
        //
    }

    /**
     * Handle the TravelOrder "deleted" event.
     */
    public function deleted(TravelOrder $travelOrder): void
    {
        //implementar aqui pra disparo de  email
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
