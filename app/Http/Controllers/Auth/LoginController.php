<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\InvalidCredentialsException;
use Facades\App\Services\Auth\TwoFactorAuthenticationService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $this->validator(request()->all())->validate();

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            throw new InvalidCredentialsException(401);
        }

        return $this->respondWithToken($token);
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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Support\Facades\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data, 
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:3'],
            ]
        );
    }

    public function authenticated()
    {
        $user = auth()->user();
        $user->token_2fa_expiry = Carbon::now();
        $user->save();
        return redirect('/admin');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function sendtwoFactorToken() {
        $via = TwoFactorAuthenticationService::sendToken();

        if($via) {
            return response()->json(
                ['message' => "You should receive a message via {$via} with your token"]
            );
        }
        return response()->json(['message'=> "Failed sending token"], 422);
    }

    public function postTwoFactorToken() {
        $verified = TwoFactorAuthenticationService::verifyToken();
        
        if($verified) {
            return response()->json(
                ['message' => "Token verified successfully"]
            );
        }

        return response()->json(
            ['message' => "The token provided is not valid"],
            422
        );
    }
}
