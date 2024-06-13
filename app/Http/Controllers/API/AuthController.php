<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseFormatter;
use App\Helpers\ResponseFormatterEntity;
use App\Models\User;


class AuthController extends Controller
{

    public function Login(Request $request)
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $login = Auth::attempt([
                'email' => $request->username,
                'password' => $request->password,
            ]);

            if (!$login) {
                $login = Auth::attempt([
                    'phone' => $request->username,
                    'password' => $request->password,
                ]);

                if (!$login) {
                    return ResponseFormatter::error('Unauthorized', 'Authentication failed', 401);
                }
            }

            $user = $user = Auth::user();

            if (!$user) {
                return ResponseFormatter::error(
                    null, 'User Not Found', 404
                );
            }

            // Formater user
            $formater = new ResponseFormatterEntity();
            $formater_user = $formater->GetUser($user);

            //token
            $token['token'] = $user->createToken('appToken')->accessToken;

            return ResponseFormatter::success([
                'user' => $formater_user,
                'authorization' => [
                    'type' => 'bearer',
                    'token' => $token,
                ]
            ], 'Login Success');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Something went wrong', 500);
        }
    }

    public function Logout(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();
            return ResponseFormatter::success(null, 'Token Revoked Successfully');
        } else {
            return ResponseFormatter::error('User not authenticated', 'Logout failed', 401);
        }
    }

}
