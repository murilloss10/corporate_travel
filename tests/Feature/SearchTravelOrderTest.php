<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SearchTravelOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a busca de ordens de viagem por ID corretamente.
     */
    public function testSearchTravelOrderByIdCorrect(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('get', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'id'                => $travel->id,
                'user_id'           => $user->id,
                'city'              => $travel->city,
                'state'             => $travel->state,
                'country'           => $travel->country,
                'status'            => $travel->status,
                'user'              => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                ]
            ]);
    }
    
    /**
     * Testa a busca de ordens de viagem por ID inexiistente.
     */
    public function testSearchNonExistentTravelOrderById(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('get', "/api/travel-orders/1")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Ordem de viagem não encontrada.'
            ]);
    }

    /**
     * Testa a busca de ordens de viagem por ID com usuário diferente do solicitante.
     */
    public function testSearchTravelOrderByIdWithDifferentRequester(): void
    {
        $user   = User::factory()->create();
        $user1  = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($user1, ['user-permission']);
        
        $this
            ->json('get', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => 'Você não tem permissão para visualizar esta ordem de viagem.'
            ]);
    }
    
    /**
     * Testa a busca de todas as ordens de viagem usando a permissão de administrador e sem filtros passados por query parameters.
     */
    public function testSearchAllTravelOrderWithAdminPermissionAndNoFilters(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $travelOrders->toArray(),
                'first_page_url'    => url('/api/travel-orders?page=1'),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url('/api/travel-orders?page=1'),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url('/api/travel-orders?page=1'),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => $totalOrders,
                'total'             => $totalOrders
            ]);
    }
    
    /**
     * Testa a busca de todas as ordens de viagem com filtro de cidade passado por query parameter.
     */
    public function testSearchAllTravelOrderWithOnlyCityParameter(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->where(['city' => $travelOrders[5]->city])
            ->get();

        $cityEncoded = rawurlencode($travelOrders[5]->city);

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?city={$travelOrders[5]->city}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?city={$cityEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?city={$cityEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?city={$cityEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }
    
    /**
     * Testa a busca de todas as ordens de viagem com filtro de estado passado por query parameter.
     */
    public function testSearchAllTravelOrderWithOnlyStateParameter(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->where(['state' => $travelOrders[6]->state])
            ->get();

        $stateEncoded = rawurlencode($travelOrders[6]->state);
        
        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?state={$travelOrders[6]->state}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?state={$stateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?state={$stateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?state={$stateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }
    
    /**
     * Testa a busca de todas as ordens de viagem com os filtros de cidade e estado passados por query parameter.
     */
    public function testSearchAllTravelOrderWithCityAndStateParameters(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->where([['city', $travelOrders[2]->city], ['state', $travelOrders[2]->state]])
            ->get();

        $cityEncoded = rawurlencode($travelOrders[2]->city);
        $stateEncoded = rawurlencode($travelOrders[2]->state);

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?city={$travelOrders[2]->city}&state={$travelOrders[2]->state}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?city={$cityEncoded}&state={$stateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?city={$cityEncoded}&state={$stateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?city={$cityEncoded}&state={$stateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }
    
    /**
     * Testa a busca de todas as ordens de viagem com os filtros de data de partida e retorno passados por query parameter.
     */
    public function testSearchAllTravelOrderWithStartAndEndDateParameters(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->whereDate('departure_date', '>=', $travelOrders[2]->departure_date->format('Y-m-d'))
            ->whereDate('return_date', '<=', $travelOrders[2]->return_date->format('Y-m-d'))
            ->get();

        $startDateEncoded = rawurlencode($travelOrders[2]->departure_date->format('Y-m-d'));
        $endDateEncoded = rawurlencode($travelOrders[2]->return_date->format('Y-m-d'));

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?startDate={$travelOrders[2]->departure_date->format('Y-m-d')}&endDate={$travelOrders[2]->return_date->format('Y-m-d')}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }

    /**
     * Testa a busca de todas as ordens de viagem com o filtro de data de partida passada por query parameter.
     */
    public function testSearchAllTravelOrderWithOnlyStartAndParameter(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->whereDate('departure_date', '>=', $travelOrders[2]->departure_date->format('Y-m-d'))
            ->get();

        $startDateEncoded = rawurlencode($travelOrders[2]->departure_date->format('Y-m-d'));

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?startDate={$travelOrders[2]->departure_date->format('Y-m-d')}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?startDate={$startDateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?startDate={$startDateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?startDate={$startDateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }

    /**
     * Testa a busca de todas as ordens de viagem com o filtro de data de retorno passada por query parameter.
     */
    public function testSearchAllTravelOrderWithOnlyEndDateParameter(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->with('user:id,name')
            ->whereDate('return_date', '<=', $travelOrders[2]->return_date->format('Y-m-d'))
            ->get();

        $endDateEncoded = rawurlencode($travelOrders[2]->return_date->format('Y-m-d'));

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('get', "/api/travel-orders?endDate={$travelOrders[2]->return_date->format('Y-m-d')}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?endDate={$endDateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?endDate={$endDateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?endDate={$endDateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }

    /**
     * Testa a busca de todas as ordens de viagem com o filtro de status passado por query parameter.
     */
    public function testSearchAllTravelOrderWithStatusParameter(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->withTrashed()
            ->where('status', $travelOrders[2]->status)
            ->with('user:id,name')
            ->get();

        $statusEncoded = rawurlencode($travelOrders[2]->status);

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json(
                'get', 
                "/api/travel-orders?status={$statusEncoded}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?status={$statusEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?status={$statusEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?status={$statusEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }

    /**
     * Testa a busca de todas as ordens de viagem com todos os filtros passados por query parameter.
     */
    public function testSearchAllTravelOrderWithAllParameters(): void
    {
        $totalOrders    = 10;
        $travelOrders   = TravelOrder::factory($totalOrders)->create();
        $user           = User::factory()->create(['role' => 'admin']);
        $searchOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->withTrashed()
            ->where([['status', $travelOrders[2]->status], ['city', $travelOrders[2]->city], ['state', $travelOrders[2]->state]])
            ->whereDate('departure_date', '>=', $travelOrders[2]->departure_date->format('Y-m-d'))
            ->whereDate('return_date', '<=', $travelOrders[2]->return_date->format('Y-m-d'))
            ->with('user:id,name')
            ->get();

        $statusEncoded      = rawurlencode($travelOrders[2]->status);
        $cityEncoded        = rawurlencode($travelOrders[2]->city);
        $stateEncoded       = rawurlencode($travelOrders[2]->state);
        $startDateEncoded   = rawurlencode($travelOrders[2]->departure_date->format('Y-m-d'));
        $endDateEncoded     = rawurlencode($travelOrders[2]->return_date->format('Y-m-d'));

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json(
                'get', 
                "/api/travel-orders?status={$statusEncoded}&city={$cityEncoded}&state={$stateEncoded}&startDate={$startDateEncoded}&endDate={$endDateEncoded}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $searchOrders->toArray(),
                'first_page_url'    => url("/api/travel-orders?status={$statusEncoded}&city={$cityEncoded}&state={$stateEncoded}&startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url("/api/travel-orders?status={$statusEncoded}&city={$cityEncoded}&state={$stateEncoded}&startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url("/api/travel-orders?status={$statusEncoded}&city={$cityEncoded}&state={$stateEncoded}&startDate={$startDateEncoded}&endDate={$endDateEncoded}&page=1"),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($searchOrders),
                'total'             => count($searchOrders),
            ]);
    }

    /**
     * Testa a busca de todas as ordens de viagem do usuário autenticado (token de usuário) e sem filtros passados por query parameters.
     */
    public function testSearchAllTravelOrderWithUserPermissionAndNoFilters(): void
    {
        $totalOrders = 10;
        TravelOrder::factory($totalOrders)->create();

        $user           = User::find(1);
        $travelOrders   = TravelOrder::select('id', 'user_id', 'city', 'state', 'country', 'departure_date', 'return_date', 'status', 'created_at', 'updated_at')
            ->where('user_id', $user->id)->with('user:id,name')->get();

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('get', "/api/travel-orders")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'current_page'      => 1,
                'data'              => $travelOrders->toArray(),
                'first_page_url'    => url('/api/travel-orders?page=1'),
                'from'              => 1,
                'last_page'         => 1,
                'last_page_url'     => url('/api/travel-orders?page=1'),
                'links'             => [
                    [
                        'url'       => null,
                        'label'     => 'pagination.previous',
                        'active'    => false
                    ],
                    [
                        'url'       => url('/api/travel-orders?page=1'),
                        'label'     => '1',
                        'active'    => true
                    ],
                    [
                        'url'       => null,
                        'label'     => 'pagination.next',
                        'active'    => false
                    ]
                ],
                'next_page_url'     => null,
                'path'              => url('/api/travel-orders'),
                'per_page'          => 20,
                'prev_page_url'     => null,
                'to'                => count($travelOrders),
                'total'             => count($travelOrders),
            ]);
    }
    
}
