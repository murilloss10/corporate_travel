<?php

namespace Tests\Feature;

use App\Jobs\UpdatingTravelOrderJob;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTravelOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a atualização de uma ordem de viagem com status de 'Aprovado'.
     * Verifica também se a fila de atualização de ordem de viagem foi acionada.
     */
    public function testUpdateTravelOrderCorrectApprovalStatus(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Solicitado']);

        Queue::fake();
        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'id'        => 1,
                'user_id'   => $user->id,
                'status'    => 'Aprovado',
            ]);

        Queue::assertPushed(UpdatingTravelOrderJob::class);
    }
    
    /**
     * Testa a avaliação de uma ordem de viagem com status de 'Cancelado'.
     */
    public function testUpdateTravelOrderCorrectDisapprovalStatus(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Solicitado']);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Cancelado'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'id'        => 1,
                'user_id'   => $user->id,
                'status'    => 'Cancelado',
            ]);
    }
    
    /**
     * Testa a atualização de uma ordem de viagem sem informar o status.
     */
    public function testUpdateTravelOrderWithoutStatusField(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}")
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'status' => [
                        'O status é obrigatório.'
                    ]
                ],
                'message' => 'O status é obrigatório.',
            ]);
    }

    /**
     * Testa a atualização de uma ordem sem passar o ID.
     */
    public function testUpdateTravelOrderWithoutTravelOrder(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders")
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'message' => 'Requisição inválida.',
            ]);
    }
    
    /**
     * Testa a atualização de uma ordem de viagem com status incorreto.
     */
    public function testUpdateTravelOrderWithIncorrectStatus(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Qualquer'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'status' => [
                        "O status deve ser 'Aprovado' ou 'Cancelado'."
                    ]
                ],
                'message' => "O status deve ser 'Aprovado' ou 'Cancelado'.",
            ]);
    }
    
    /**
     * Testa a atualização de uma ordem de viagem sem permissão (sem token).
     */
    public function testUpdateTravelOrderWithoutPermission(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($admin);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => "Ação não autorizada.",
            ]);
    }

    /**
     * Testa a atualização de uma ordem de viagem com permissão de usuário (com token de usuário).
     */
    public function testUpdateTravelOrderWithUserPermission(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create();
        $travel = TravelOrder::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($admin, ['user-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => "Ação não autorizada.",
            ]);
    }
    
    /**
     * Testa a atualização de uma ordem de viagem inexistente.
     */
    public function testUpdateNonExistentTravelOrder(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/1", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                'message' => "Ordem de viagem não encontrada.",
            ]);
    }

    /**
     * Testa a atualização de uma ordem de viagem com o mesmo solicitante.
     */
    public function testUpdateTravelOrderWithSameRequester(): void
    {
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $admin->id]);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Você não tem permissão para alterar o status desta ordem de viagem.',
            ]);
    }

    /**
     * Testa a atualização de uma ordem de viagem já avaliada.
     */
    public function testUpdateTravelOrderAlreadyAssessed(): void
    {
        $user   = User::factory()->create();
        $admin  = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->create(['user_id' => $user->id, 'status' => 'Cancelado']);

        Passport::actingAs($admin, ['admin-permission']);

        $this
            ->json('patch', "/api/travel-orders/{$travel->id}", [
                'status' => 'Aprovado'
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Esta ordem de viagem já foi avaliada.',
            ]);
    }

}
