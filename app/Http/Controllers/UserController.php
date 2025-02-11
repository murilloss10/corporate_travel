<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/users",
     *      summary="Cria um usuário.",
     *      description="Cria um usuário.",
     *      tags={"NoPermission"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", format="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *              @OA\Property(property="role", type="string", format="string", example="user|admin"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuário criado com sucesso.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              @OA\Property(property="created_at", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="updated_at", type="string", example="2025-02-11 10:00:00"),
     *              @OA\Property(property="token", type="string", example="bearertoken...."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erro de validação.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array",
     *                      @OA\Items(type="string", example="O nome é obrigatório."),
     *                  ),
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="O e-mail é obrigatório."),
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="A senha é obrigatória."),
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */
    public function store(CreatesUserRequest $request): JsonResponse
    {
        $user           = User::create($request->validated());
        $scope          = $user->role === 'user' ? 'user-permission' : 'admin-permission';
        $user->token    = $user->createToken('token', ["$scope"])->accessToken;

        return response()->json($user, 201);
    }
}
