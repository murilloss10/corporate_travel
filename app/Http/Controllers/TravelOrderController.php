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

    /**
     *  @OA\Get(
     *      path="/api/travel-orders",
     *      summary="Retorna todas as ordens de viagens.",
     *      description="Retorna todas as ordens de viagens.",
     *      tags={"UserPermission", "AdminPermission"},
     *      security={{ "bearerAuth":{} }},
     *      @OA\Parameter(
     *          name="status",
     *          description="Filtro de busca por status",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", description="Status disponíveis: 'Aprovado', 'Solicitado' e 'Cancelado'", enum={"Aprovado", "Pendente", "Rejeitado"}),
     *      ),
     *      @OA\Parameter(
     *          name="city",
     *          description="Filtro de busca por cidade",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", description="Digite a cidade a ser buscada", example="Belo Horizonte"),
     *      ),
     *      @OA\Parameter(
     *          name="state",
     *          description="Filtro de busca por estado",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", description="Digite o estado a ser buscado", example="Minas Gerais"),
     *      ),
     *      @OA\Parameter(
     *          name="startDate",
     *          description="Filtro de busca por data de partida",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="date", description="Selecione a data de partida", example="2024-01-01"),
     *      ),
     *      @OA\Parameter(
     *          name="endDate",
     *          description="Filtro de busca por data de retorno",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="date", description="Selecione a data de retorno", example="2025-12-31"),
     *      ),
     *      @OA\Parameter(
     *          name="perPage",
     *          description="Filtro de resultados por página",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer", description="Digite a quantidade de itens por página", example=5),
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Lista de ordens de viagens.",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="current_page",
     *                  type="integer",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="user_id", type="integer", example=1),
     *                      @OA\Property(property="city", type="string", example="Belo Horizonte"),
     *                      @OA\Property(property="state", type="string", example="Minas Gerais"),
     *                      @OA\Property(property="country", type="string", example="Brasil"),
     *                      @OA\Property(property="departure_date", type="string", example="2025-05-11 00:00:00"),
     *                      @OA\Property(property="return_date", type="string", example="2025-06-11 00:00:00"),
     *                      @OA\Property(property="status", type="string", example="Aprovado"),
     *                      @OA\Property(property="created_at", type="string", example="2025-02-11 00:00:00"),
     *                      @OA\Property(property="updated_at", type="string", example="2025-02-11 00:00:00"),
     *                      @OA\Property(
     *                          property="user",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="John Doe")
     *                      ),
     *                  )
     *              ),
     *              @OA\Property(property="first_page_url", type="string",  example="http://localhost:8081/api/travel-orders?page=1"),
     *              @OA\Property(property="from", type="integer",  example="1"),
     *              @OA\Property(property="last_page", type="integer",  example="1"),
     *              @OA\Property(property="last_page_url", type="string",  example="http://localhost:8081/api/travel-orders?page=2"),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="url", type="string", example="http://localhost/api/travel-orders?page=1"),
     *                      @OA\Property(property="label", type="integer", example=1),
     *                      @OA\Property(property="active", type="boolean", example=true)
     *                  ),
     *              ),
     *              @OA\Property(property="next_page_url", type="string",  example=null),
     *              @OA\Property(property="path", type="string",  example="http://localhost:8081/api/travel-orders"),
     *              @OA\Property(property="per_page", type="integer",  example=20),
     *              @OA\Property(property="prev_page_url", type="string",  example=null),
     *              @OA\Property(property="to", type="integer",  example=1),
     *              @OA\Property(property="total", type="integer",  example=1)
     *         ),
     *     ),
     *  )
     */
    public function index(): ?JsonResponse
    {
        return $this->travelOrderService->allTravelOrders();
    }

    /**
     *  @OA\Post(
     *      path="/api/travel-orders",
     *      summary="Cria uma nova ordem de viagem.",
     *      description="Cria uma nova ordem de viagem.",
     *      tags={"UserPermission"},
     *      security={{ "bearerAuth":{} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="city", type="string", example="Belo Horizonte"),
     *              @OA\Property(property="state", type="string", example="Minas Gerais"),
     *              @OA\Property(property="country", type="string", example="Brasil"),
     *              @OA\Property(property="departure_date", type="string", example="2025-02-11"),
     *              @OA\Property(property="return_date", type="string", example="2025-02-11"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Criado com sucesso.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="city", type="string", example="Belo Horizonte"),
     *              @OA\Property(property="state", type="string", example="Minas Gerais"),
     *              @OA\Property(property="country", type="string", example="Brasil"),
     *              @OA\Property(property="departure_date", type="string", example="2025-02-11 00:00:00"),
     *              @OA\Property(property="return_date", type="string", example="2025-02-11 00:00:00"),
     *              @OA\Property(property="created_at", type="string", example="2025-02-11 00:00:00"),
     *              @OA\Property(property="updated_at", type="string", example="2025-02-11 00:00:00")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autorizado.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Ação não autorizada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ação não autorizada."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ação não autorizada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message", type="string", example="A cidade é obrigatória."
     *              ),
     *              @OA\Property(
     *                  property="errors", 
     *                  type="object",
     *                  @OA\Property(
     *                      property="city",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="A cidade é obrigatória."
     *                      ),
     *                  ),
     *              ),
     *          )
     *      ),
     *  )
     */
    public function store(CreatesTravelOrderRequest $request): ?JsonResponse
    {
        try {
            return $this->travelOrderService->createsTravelOrder($request);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     *  @OA\Get(
     *      path="/api/travel-orders/{travel_order}",
     *      summary="Retorna uma ordem de viagem específica.",
     *      description="Retorna uma ordem de viagem específica.",
     *      tags={"UserPermission", "AdminPermission"},
     *      security={{ "bearerAuth":{} }},
     *      @OA\Parameter(
     *          name="travel_order",
     *          description="ID da ordem de viagem",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", description="ID da ordem de viagem", example=1),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Dados retornados com sucesso.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="city", type="string", example="Belo Horizonte"),
     *              @OA\Property(property="state", type="string", example="Minas Gerais"),
     *              @OA\Property(property="country", type="string", example="Brasil"),
     *              @OA\Property(property="departure_date", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="return_date", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="status", type="string", example="Aprovado"),
     *              @OA\Property(property="created_at", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="updated_at", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="deleted_at", type="string", example=null),
     *              @OA\Property(
     *                  property="user", 
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autorizado.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Ação não autorizada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Você não tem permissão para visualizar esta ordem de viagem."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ordem de viagem não encontrada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ordem de viagem não encontrada."),
     *          ),
     *      ),
     *  )
     */
    public function show(int $travel_order): ?JsonResponse
    {
        try {
            return $this->travelOrderService->getsTravelOrder($travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     *  @OA\Patch(
     *      path="/api/travel-orders/{travel_order}",
     *      summary="Avalia uma ordem de viagem.",
     *      description="Avalia uma ordem de viagem.",
     *      tags={"AdminPermission"},
     *      security={{ "bearerAuth":{} }},
     *      @OA\Parameter(
     *          name="travel_order",
     *          description="ID da ordem de viagem",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", description="ID da ordem de viagem", example=1),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="string", example="Aprovado|Cancelado")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Avaliado com sucesso.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="status", type="string", example="Aprovado"),
     *              @OA\Property(property="updated_at", type="string", example="2025-02-11 00:00:00")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Requisição inválida.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Requisição inválida."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autorizado.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Ação não autorizada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ação não autorizada."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ordem de viagem não encontrada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ordem de viagem não encontrada."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Status inválido.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="O status deve ser 'Aprovado' ou 'Cancelado'."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="status", type="array",
     *                      @OA\Items(type="string", example="O status deve ser 'Aprovado' ou 'Cancelado'.")
     *                  )
     *              )
     *          )
     *      ),
     *  )
     */
    public function update(UpdatesTravelOrderStatusRequest $request, int $travel_order)
    {
        try {
            return $this->travelOrderService->updatesTravelOrderStatus($request, $travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/travel-orders/{travel_order}",
     *      summary="Cancela uma ordem de viagem.",
     *      description="Cancela uma ordem de viagem.",
     *      tags={"UserPermission"},
     *      security={{ "bearerAuth":{} }},
     *      @OA\Parameter(
     *          name="travel_order",
     *          description="ID da ordem de viagem",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", description="ID da ordem de viagem", example=1),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Cancelado com sucesso.",
     *          @OA\JsonContent(
     *              type="object"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Requisição inválida.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Requisição inválida."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Não autorizado.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Ação não autorizada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Você não tem permissão para cancelar esta ordem de viagem."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ordem de viagem não encontrada.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Ordem de viagem não encontrada."),
     *          ),
     *      ),
     *  )
     */
    public function destroy(int $travel_order): ?JsonResponse
    {
        try {
            return $this->travelOrderService->cancelTravelOrder($travel_order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
}
