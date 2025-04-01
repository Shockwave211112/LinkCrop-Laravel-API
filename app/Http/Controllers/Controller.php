<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;

/**
 * @OA\Info(
 *    title="Links API",
 *    description="...",
 *    version="1.0.0",
 * )
 * @OA\Schema(
 *      schema="MessageResponse",
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="Info message"
 *      )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
