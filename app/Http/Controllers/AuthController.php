<?php

namespace App\Http\Controllers;

use App\Http\Helper\JsonApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    function login(Request $request){
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)){
            $user_details = array();
            $user_details['first_name'] = Auth::user()->first_name;
            $user_details['last_name'] = Auth::user()->last_name;
            $user_details['username'] = Auth::user()->username;
            $user_details['email'] = Auth::user()->email;

            return JsonApiResponse::success('Successfully Logged in', [
                'access_token' => Auth::user()->createToken(Str::random(50))->accessToken,
                'user_details' => $user_details
            ]);
        } else {
            return JsonApiResponse::error('Please enter correct credentials.', 422);
        }
    }

    function logout(Request $request){
        $request->validate([
            'username' => 'exists:users'
        ]);

        Auth::user()->tokens()->update([
            'revoked' => true
        ]);

        return JsonApiResponse::success('Successfully logged out.');
    }
}
