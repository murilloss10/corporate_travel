<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="0.0.0",
 *      title="Corporate Travel API",
 *      description="Documentação da API.",
 *      @OA\Contact(
 *          email="murilloss10@gmail.com",
 *          name="Murillo Santos"
 *      ),
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
