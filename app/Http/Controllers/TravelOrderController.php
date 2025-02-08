<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesTravelOrderRequest;
use App\Http\Requests\UpdatesTravelOrderStatusRequest;
use App\Services\TravelOrderService;
use Exception;
use Illuminate\Http\JsonResponse;

class TravelOrderController extends Controller
{
    public function __construct(
        private TravelOrderService $travelOrderService
    ) {}

    public function index(): ?JsonResponse
    {
        return $this->travelOrderService->allTravelOrders();
    }

    public function store(CreatesTravelOrderRequest $request): ?JsonResponse
    {
        try {
            return $this->travelOrderService->createsTravelOrder($request);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function show(int $travel_order): ?JsonResponse
    {
        try {
            return $this->travelOrderService->getsTravelOrder($travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(UpdatesTravelOrderStatusRequest $request, int $travel_order)
    {
        try {
            return $this->travelOrderService->updatesTravelOrderStatus($request, $travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy(int $travel_order): ?JsonResponse
    {
        try {
            return $this->travelOrderService->cancelTravelOrder($travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
}
