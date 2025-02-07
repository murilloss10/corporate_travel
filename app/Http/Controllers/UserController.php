<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function store(CreatesUserRequest $request): JsonResponse
    {
        $user           = User::create($request->validated());
        $scope          = $user->role === 'user' ? 'user-permission' : 'admin-permission';
        $user->token    = $user->createToken('token', ["$scope"])->accessToken;

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
