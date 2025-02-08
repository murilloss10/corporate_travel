<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\CreatesTravelOrderRequest;
use App\Http\Requests\UpdatesTravelOrderStatusRequest;
use App\Models\TravelOrder;
use Exception;
use Illuminate\Http\JsonResponse;

class TravelOrderService
{
    public function __construct(
        protected TravelOrder $travelOrder
    ) {}

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function allTravelOrders(): ?JsonResponse
    {
        $allTravelOrders = $this->travelOrder
            ->when(request()->has('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->with('user:id,name')
            ->paginate(request('perPage') ?? 20);
        
        return response()->json($allTravelOrders, 200);
    }

    /**
     * @param int $travel_oder
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getsTravelOrder(int $travel_order): ?JsonResponse
    {
        $travelOrderFinded = $this->travelOrder
            ->with('user:id,name,email,created_at,updated_at')
            ->find($travel_order);
        
        if (!$travelOrderFinded)
            throw new Exception('Ordem de viagem não encontrada.', 404);

        return response()->json($travelOrderFinded, 200);
    }

    /**
     * @param \App\Http\Requests\CreatesTravelOrderRequest $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function createsTravelOrder(CreatesTravelOrderRequest $request): ?JsonResponse
    {
        $travelOrderCreated = $this->travelOrder->create([
            'user_id'           => $request->user()->id,
            'city'              => $request->safe()->city,
            'state'             => $request->safe()->state,
            'country'           => $request->safe()->country,
            'departure_date'    => $request->safe()->departure_date,
            'return_date'       => $request->safe()->return_date,
        ]);

        return response()->json($travelOrderCreated, 201);
    }

    /**
     * @param \App\Http\Requests\UpdatesTravelOrderStatusRequest $request
     * @param int $travel_order
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function updatesTravelOrderStatus(UpdatesTravelOrderStatusRequest $request, int $travel_order): ?JsonResponse
    {
        $travelOrderUpdating = $this->travelOrder->select('id', 'status')->find($travel_order);
        $travelOrderUpdating->status = $request->safe()->status;
        $travelOrderUpdating->save();

        return response()->json($travelOrderUpdating, 200);
    }

    /**
     * @param int $travel_order
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function cancelTravelOrder(int $travel_order): ?JsonResponse
    {
        $travelOrderDeleting = $this->travelOrder->select('id')->find($travel_order);

        if (!$travelOrderDeleting)
            throw new Exception('Ordem de viagem não encontrada.', 404);

        if ($travelOrderDeleting->delete())
            return response()->json([], 204);

        throw new Exception('Não foi possível cancelar a ordem de viagem. Tente novamente mais tarde.', 500);
    }
}