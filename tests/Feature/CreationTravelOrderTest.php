<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreationTravelOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a criação correta de uma ordem de viagem.
     */
    public function testCreationTravelOrderCorrect(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->make()->toArray();

        Passport::actingAs($user, ['user-permission']);

        $this
            ->json('post', '/api/travel-orders', $travel)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'user_id'           => $user->id,
                'city'              => $travel['city'],
                'state'             => $travel['state'],
                'country'           => $travel['country'],
                'departure_date'    => $travel['departure_date'],
                'return_date'       => $travel['return_date'],
                'id'                => 1,
            ]);
    }

    /**
     * Testa a crição de uma ordem de viagem sem autenticação (sem token).
     */
    public function testCreationTravelOrderWithoutAuthentication(): void
    {
        $travel = TravelOrder::factory()->make()->toArray();

        $this
            ->json('post', '/api/travel-orders', $travel)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * Testa a criação de uma ordem de viagem com permissão de administrador (token de usuário).
     */
    public function testCreationTravelOrderWithAdminPermission(): void
    {
        $user   = User::factory()->create(['role' => 'admin']);
        $travel = TravelOrder::factory()->make()->toArray();

        Passport::actingAs($user, ['admin-permission']);

        $this
            ->json('post', '/api/travel-orders', $travel)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Ação não autorizada.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem permissão (token de usuário).
     */
    public function testCreationTravelOrderWithoutPermission(): void
    {
        $user   = User::factory()->create();
        $travel = TravelOrder::factory()->make()->toArray();

        Passport::actingAs($user);

        $this
            ->json('post', '/api/travel-orders', $travel)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertExactJson([
                'message' => 'Ação não autorizada.'
            ]);
    }

    /**
     * Testa a criação de uma ordem de viagem com datas inválidas.
     */
    public function testCreationTravelOrderWithInvalidDates(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $this
            ->json('post', '/api/travel-orders', [
                'user_id'           => $user->id,
                'city'              => fake()->city(),
                'state'             => fake()->state(),
                'country'           => fake()->country(),
                'departure_date'    => 'Invalid date format',
                'return_date'       => 'Invalid date format',
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'departure_date' => [
                        'A data de partida deve ser uma data válida.',
                        'A data de partida deve ser posterior à data atual.'
                    ],
                    'return_date' => [
                        'A data de retorno deve ser uma data válida.'
                    ]
                ],
                'message' => 'A data de partida deve ser uma data válida. (and 2 more errors)'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem o campo cidade.
     */
    public function testCreationTravelOrderWithoutCityField(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('+1 day', '+30 days');
        $returnDate = fake()->dateTimeBetween($departureDate, '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'state'             => fake()->state(),
                'country'           => fake()->country(),
                'departure_date'    => $departureDate->format('Y-m-d'),
                'return_date'       => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'city' => [
                        'A cidade é obrigatória.'
                    ]
                ],
                'message' => 'A cidade é obrigatória.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem o campo estado.
     */
    public function testCreationTravelOrderWithoutStateField(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('+1 day', '+30 days');
        $returnDate = fake()->dateTimeBetween($departureDate, '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'              => fake()->city(),
                'country'           => fake()->country(),
                'departure_date'    => $departureDate->format('Y-m-d'),
                'return_date'       => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'state' => [
                        'O estado é obrigatório.'
                    ]
                ],
                'message' => 'O estado é obrigatório.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem o campo país.
     */
    public function testCreationTravelOrderWithoutCountryField(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('+1 day', '+30 days');
        $returnDate = fake()->dateTimeBetween($departureDate, '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'              => fake()->city(),
                'state'             => fake()->state(),
                'departure_date'    => $departureDate->format('Y-m-d'),
                'return_date'       => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'country' => [
                        'O país é obrigatório.'
                    ]
                ],
                'message' => 'O país é obrigatório.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem o campo data de partida.
     */
    public function testCreationTravelOrderWithoutDepartureDateField(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $returnDate = fake()->dateTimeBetween('+1 day', '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'          => fake()->city(),
                'state'         => fake()->state(),
                'country'       => fake()->country(),
                'return_date'   => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'departure_date' => [
                        'A data de partida é obrigatória.'
                    ]
                ],
                'message' => 'A data de partida é obrigatória.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem sem o campo data de retorno.
     */
    public function testCreationTravelOrderWithoutReturnDateField(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('+1 day', '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'              => fake()->city(),
                'state'             => fake()->state(),
                'country'           => fake()->country(),
                'departure_date'    => $departureDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'return_date' => [
                        'A data de retorno é obrigatória.'
                    ]
                ],
                'message' => 'A data de retorno é obrigatória.'
            ]);
    }
    
    /**
     * Testa a criação de uma ordem de viagem com a data de partida no passado.
     */
    public function testCreationTravelOrdertDepartureDateBeforeToday(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('-20 days', '-10 days');
        $returnDate = fake()->dateTimeBetween($departureDate, '+40 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'              => fake()->city(),
                'state'             => fake()->state(),
                'country'           => fake()->country(),
                'departure_date'    => $departureDate->format('Y-m-d'),
                'return_date'       => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'departure_date' => [
                        'A data de partida deve ser posterior à data atual.'
                    ]
                ],
                'message' => 'A data de partida deve ser posterior à data atual.'
            ]);
    }

    /**
     * Testa a criação de uma ordem de viagem com a data de retorno antes da data de partida.
     */
    public function testCreationTravelOrdertReturnDateBeforeDepartureDate(): void
    {
        $user = User::factory()->create();

        Passport::actingAs($user, ['user-permission']);

        $departureDate = fake()->dateTimeBetween('+4 day', '+6 days');
        $returnDate = fake()->dateTimeBetween('+1 day', '+3 days');

        $this
            ->json('post', '/api/travel-orders', [
                'city'              => fake()->city(),
                'state'             => fake()->state(),
                'country'           => fake()->country(),
                'departure_date'    => $departureDate->format('Y-m-d'),
                'return_date'       => $returnDate->format('Y-m-d'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'errors' => [
                    'return_date' => [
                        'A data de retorno deve ser igual ou posterior à data de partida.'
                    ]
                ],
                'message' => 'A data de retorno deve ser igual ou posterior à data de partida.'
            ]);
    }
}
