<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if(auth()->attempt(['email' => $request->email, 'password' => $request->password])){ 
            $auth = auth()->user(); 
            $success['token'] =  $auth->createToken('LaravelSanctumAuth')->plainTextToken; 
            $success['name'] =  $auth->name;
            
            return $this->handleResponse($success, 'User logged-in!');
        } 
        else{ 
            return $this->handleError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('LaravelSanctumAuth')->plainTextToken;
        $success['name'] =  $user->name;
        
        return $this->handleResponse($success, 'User successfully registered!');
    }
}
