<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $result = new ResponseApi;
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
            'role' => 'required|in:admin,kasir',
        ]);

        if ($validator->fails()) {
            $result->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $result->title('Register Failed');
            $result->message('Validation error');
            $result->formError($validator->errors());
            return $result;
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->activated = $request->role == 'admin' ? true : false;
        $user->save();

        $result->statusCode(Response::HTTP_CREATED);
        $result->message('Created');
        if ($request->role == 'admin') {
            $result->title('Register Successful');
        } else {
            $result->title('Register Successful, Please wait for admin approval');
        }
        $result->data($user);
        return $result;
    }

    public function login(Request $request, User $user): JsonResponse
    {
        $result = new ResponseApi;
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            $result->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $result->title('Login Failed');
            $result->message('Validation error');
            $result->formError($validator->errors());
            return $result;
        }


        $user = User::where('email', $request->email)->first();
        if ($user->role != 'admin' && !$user->activated) {
            $result->statusCode(Response::HTTP_UNAUTHORIZED);
            $result->title('Login Failed');
            $result->error('Account not activated');
            return $result;
        }

        if (!$token = Auth::attempt($credentials)) {
            $result->statusCode(Response::HTTP_UNAUTHORIZED);
            $result->title('Login Failed');
            $result->error('Invalid Credentials');
            return $result;
        }

        $result->statusCode(Response::HTTP_OK);
        $result->title('Login Successful');
        $result->data([
            'user' => auth()->user(),
            'token' => $token
        ]);
        return $result;
    }

    public function logout(): JsonResponse
    {
        auth()->logout(true);

        $result = new ResponseApi;
        $result->statusCode(Response::HTTP_OK);
        $result->title('Logout Successful');
        $result->message('You are logged out');
        $result->data([]);
        return $result;
    }

    public function me(): JsonResponse
    {
        $result = new ResponseApi;
        $result->statusCode(Response::HTTP_OK);
        $result->title('User Profile');
        $result->data(auth()->user());
        return $result;
    }
}
