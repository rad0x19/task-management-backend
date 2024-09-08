<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\AuthServices;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequests;
use App\Http\Requests\Auth\RegistrationRequests;

class AuthController extends Controller
{
    
    public $authServices;

    public function __construct(){
        $this->authServices = new AuthServices();
    }

    // login controller
    public function login(LoginRequests $request){
        try{
            return $this->authServices->loginProcess($request->email, $request->password);
        }catch(Exception $err){
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

    // registration controller
    public function register(RegistrationRequests $request){
        DB::beginTransaction();
        try{
            $this->authServices->registrationProcess($request->full_name, $request->email, $request->password);
            DB::commit();
            return response()->json([
                'message'   =>  'Registration complete.'
            ]);
        }catch(Exception $err){
            DB::rollback();
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

    // logout
    public function logout (){
        DB::beginTransaction();
        try{
            $user = Auth::user();
            $user->tokens()->delete();
            DB::commit();
            return response()->json([
                'message'   => 'you have been logged out'
            ]);
        }catch(Exception $err){
            DB::rollback();
            return response()->json([
                'message'   =>  $err->getMessage()
            ],500);
        }
    }

}
