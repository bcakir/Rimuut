<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/auth/signup",
     *     tags={"Auth"},
     *     summary="Signup",
     *     description="Returns signup user",
     * 	   @OA\Parameter(
     * 			name="name",
     *          description = "user name",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="email",
     *          description = "user email",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="password",
     *          description = "password",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="password_confirmation",
     *          description = "password confirmation",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
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
     *     ),
     * )
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email'  => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails())
        {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        User::create($input);

        return $this->sendResponse(['name' => $input['name']], 'Successfully user created.', 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Login",
     *     description="Returns login user",
     * 	   @OA\Parameter(
     * 			name="email",
     *          description = "user email",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="password",
     *          description = "password",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="string")
     * 	   ),
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
     *     ),
     * )
     */
    public function login(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
        {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $credentials = ['email' => $input['email'], 'password' => $input['password']];
        if (! Auth::attempt($credentials))
        {
            return $this->sendError('Unauthorized!', [], 401);
        }

        $user = Auth::user();
        $tokenResult = $user->createToken($user->email);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * @OA\Get (
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     description="Returns none",
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
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->sendResponse([],'Successfully logged out.', 200);
    }
}
