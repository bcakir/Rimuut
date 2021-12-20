<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Status;

class StatusController extends BaseController
{
    /**
     * @OA\Get (
     *     path="/auth/statuses",
     *     tags={"Extra endpoints"},
     *     summary="Get list of statuses",
     *     description="Returns list of statuses",
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
        return response()->json(Status::all());
    }
}
