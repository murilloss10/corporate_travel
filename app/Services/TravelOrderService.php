<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\CreatesTravelOrderRequest;
use App\Http\Requests\UpdatesTravelOrderStatusRequest;
use App\Models\TravelOrder;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
            ->withTrashed()
            ->when(request()->user()->tokenCan('user-permission'), fn(Builder $query) => $query->where('user_id', auth()->user()->id))
            ->when(request()->has('status'), fn(Builder $query) => $query->where('status', request('status')) )
            ->when(request()->has('city') && !request()->has('state'), fn(Builder $query) => $query->where('city', request('city')) )
            ->when(!request()->has('city') && request()->has('state'), fn(Builder $query) => $query->where('state', request('state')) )
            ->when(request()->has('city') && request()->has('state'), fn(Builder $query) => $query->where([['state', request('state')], ['city', request('city')]]) )
            ->when(request()->has('startDate'), fn(Builder $query) => $query->whereDate('departure_date', '>=', request('startDate')) )
            ->when(request()->has('endDate'), fn(Builder $query) => $query->whereDate('return_date', '<=', request('endDate')) )
            ->with('user:id,name')
            ->paginate(
                request('perPage') ?? 20, 
                ['id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at']
            );

        request()->has('perPage') && $allTravelOrders->appends(['perPage' => request('perPage')]);
        request()->has('status') && $allTravelOrders->appends(['status' => request('status')]);
        request()->has('city') && $allTravelOrders->appends(['city' => request('city')]);
        request()->has('state') && $allTravelOrders->appends(['state' => request('state')]);
        request()->has('startDate') && $allTravelOrders->appends(['startDate' => request('startDate')]);
        request()->has('endDate') && $allTravelOrders->appends(['endDate' => request('endDate')]);
        
        return response()->json($allTravelOrders, Response::HTTP_OK);
    }

    /**
     * @param int $travel_oder
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getsTravelOrder(int $travel_order): ?JsonResponse
    {
        $travelOrderFinded = $this->travelOrder
            ->withTrashed()
            ->with('user:id,name,email')
            ->find($travel_order);
        
        if (!$travelOrderFinded)
            throw new Exception('Ordem de viagem não encontrada.', Response::HTTP_NOT_FOUND);

        if (request()->user()->tokenCan('user-permission') && $travelOrderFinded->user_id !== auth()->user()->id)
            throw new Exception('Você não tem permissão para visualizar esta ordem de viagem.', Response::HTTP_FORBIDDEN);

        return response()->json($travelOrderFinded, Response::HTTP_OK);
    }

    /**
     * @param \App\Http\Requests\CreatesTravelOrderRequest $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function createsTravelOrder(CreatesTravelOrderRequest $request): ?JsonResponse
    {
        $travelOrderCreated = $this->travelOrder->create([
            'user_id'           => auth()->user()->id,
            'city'              => $request->safe()->city,
            'state'             => $request->safe()->state,
            'country'           => $request->safe()->country,
            'departure_date'    => $request->safe()->departure_date,
            'return_date'       => $request->safe()->return_date,
        ]);

        return response()->json($travelOrderCreated, Response::HTTP_CREATED);
    }

    /**
     * @param \App\Http\Requests\UpdatesTravelOrderStatusRequest $request
     * @param int $travel_order
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function updatesTravelOrderStatus(UpdatesTravelOrderStatusRequest $request, int $travel_order): ?JsonResponse
    {
        $travelOrderUpdating = $this->travelOrder->select('id', 'user_id', 'status')->find($travel_order);

        if (!$travelOrderUpdating)
            throw new Exception('Ordem de viagem não encontrada.', Response::HTTP_NOT_FOUND);

        if ($travelOrderUpdating->user_id === auth()->user()->id)
            throw new Exception('Você não tem permissão para alterar o status desta ordem de viagem.', Response::HTTP_FORBIDDEN);

        if ($travelOrderUpdating->status !== 'Solicitado')
            throw new Exception('Esta ordem de viagem já foi avaliada.', Response::HTTP_FORBIDDEN);

        $travelOrderUpdating->status = $request->safe()->status;
        $travelOrderUpdating->save();

        return response()->json($travelOrderUpdating, Response::HTTP_OK);
    }

    /**
     * @param int $travel_order
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function cancelTravelOrder(int $travel_order): ?JsonResponse
    {
        $travelOrderDeleting = $this->travelOrder
            ->select('id', 'user_id', 'status')
            ->where([['id', $travel_order], ['status', 'Aprovado']])
            ->first();
        
        if (!$travelOrderDeleting)
            throw new Exception('Ordem de viagem não encontrada.', Response::HTTP_NOT_FOUND);

        if (request()->user()->tokenCan('admin-permission') || $travelOrderDeleting->user_id !== auth()->user()->id)
            throw new Exception('Você não tem permissão para cancelar esta ordem de viagem.', Response::HTTP_FORBIDDEN);

        if ($travelOrderDeleting->delete())
            return response()->json([], Response::HTTP_NO_CONTENT);

        throw new Exception('Não foi possível cancelar a ordem de viagem. Tente novamente mais tarde.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}