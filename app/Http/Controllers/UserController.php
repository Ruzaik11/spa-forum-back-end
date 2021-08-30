<?php

namespace App\Http\Controllers;

use App\Http\Validators\UserApiValidator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(Request $request)
    {

        try {

            $data = $request->except(array_keys($request->query()));

            $validateRequest = UserApiValidator::register($data);

            if (!$validateRequest->fails()) {

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);

                $user->addRole('forum-user'); // adding role forum-user

                $token = auth('api')->login($user);

                return response()->json([
                    'error' => false,
                    'data' => ['user' => $user, 'token' => $this->respondWithToken($token), 'roles' => $user->roles()->get()],
                ], 200);

            } else {

                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->all(),
                ], 400);

            }

        } catch (\Throwable $th) {
            
            \Log::alert($th);
            return response()->json([
                'error' => true,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function login(Request $request)
    {

        try {

            $data = $request->except(array_keys($request->query()));
            $validateRequest = UserApiValidator::login($data);

            if (!$validateRequest->fails()) {

                $token = auth('api')->attempt($request->only('email', 'password'));

                if (!$token) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                $user = auth('api')->user();

                return response()->json([
                    'error' => false,
                    'data' => ['user' => $user, 'token' => $this->respondWithToken($token), 'roles' => $user->roles],
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->all(),
                ], 400);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => 'Internal Server Error',
            ], 500);
        }

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if(auth()->user()){
            auth()->logout();
        }

        return response()->json(['error' => true, 'message' => 'Successfully logged out' ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

}
