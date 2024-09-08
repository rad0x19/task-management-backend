<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthServices {

    // login process
    public function loginProcess($email, $password){
        $user = User::where([ 'email' => $email ]);
        if($user->count()){
            $user = $user->first();
            if(!Hash::check($password, $user->password)){
                throw ValidationException::withMessages([
                    'password'  =>  'Password is incorrect.'
                ]);
            }
            // create token
            return response()->json([
                'token' =>  $user->createToken(md5(microtime()))->plainTextToken
            ]);
        }else{
            throw ValidationException::withMessages([
                'email'  =>  'User doesn`t exists.'
            ]);
        }
    }

    public function registrationProcess($full_name, $email, $password){
        User::create([
            'name'  =>  $full_name,
            'email' =>  $email,
            'password'  =>  bcrypt($password)
        ]);
    }

}