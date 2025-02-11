<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteTravelOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a exclusão de uma ordem de viagem aprovada.
     */
    public function testDeleteTravelOrderCorrect(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Aprovado']);

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_NO_CONTENT)
            ->assertNoContent();
    }
    
    /**
     * Testa a exclusão de uma ordem de viagem com status de 'Cancelado'.
     */
    public function testDeleteTravelOrderWithCancelStatus(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Cancelado']);

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                'message' => 'Ordem de viagem não encontrada.',
            ]);
    }
    
    /**
     * Testa a exclusão de uma ordem de viagem com status de 'Solicitado'.
     */
    public function testDeleteTravelOrderWithPendingStatus(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Solicitado']);

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                'message' => 'Ordem de viagem não encontrada.',
            ]);
    }

    /**
     * Testa a exclusão de uma ordem de viagem inexistente.
     */
    public function testDeleteNonExistentTravelOrder(): void
    {
        $user   = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/1")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                'message' => 'Ordem de viagem não encontrada.',
            ]);
    }

    /**
     * Testa a exclusão de uma ordem de viagem com permisssão de administrador (token de administrador).
     */
    public function testDeleteTravelOrderWithAdminPermission(): void
    {
        $user   = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Aprovado']);

        Passport::actingAs($user, ['admin-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Você não tem permissão para cancelar esta ordem de viagem.',
            ]);
    }

    /**
     * Testa a exclusão de uma ordem de viagem sem permissão (sem token).
     */
    public function testDeleteTravelOrderWithoutPermission(): void
    {
        $user   = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Aprovado']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ]);
    }
    
    /**
     * Testa a exclusão de uma ordem de viagem com requerente diferente.
     */
    public function testDeleteTravelOrderWithDifferentRequester(): void
    {
        $user   = User::factory()->create();
        $user2  = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Aprovado']);

        Passport::actingAs($user2, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Você não tem permissão para cancelar esta ordem de viagem.',
            ]);
    }
    
    /**
     * Testa a exclusão de uma ordem de viagem sem ordem de viagem.
     */
    public function testDeleteTravelOrderWithoutTravelOrder(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);
        
        $this
            ->json('delete', "/api/travel-orders")
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'message' => 'Requisição inválida.',
            ]);
    }
}
