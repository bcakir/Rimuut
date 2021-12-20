<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends BaseController
{
    /**
     * @OA\Get (
     *     path="/auth/currencies",
     *     tags={"Extra endpoints"},
     *     summary="Get list of currencies",
     *     description="Returns list of currencies",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(Request $request)
    {
        return response()->json(Currency::all());
    }
}
