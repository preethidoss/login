<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        // Validate input before continuing
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $userData = [];
        $jwt_token = null;
        try {
            $loginResponse = Login::doLogin(request('email'), request('password'));
            if(array_key_exists('jwt_token', $loginResponse)){
//                error_log("DF API:" . var_export($loginResponse,1));
                $jwt_token = $loginResponse['jwt_token'];
                // @note why are you saving this to the db?
                $userData = Login::getUserData($loginResponse['jwt_token']);
                $model = User::firstOrNew(
                    ['email' => $userData['email']]
                );
                $model->password = Hash::make(request('password'));
                $model->first_name = $userData['first_name'];
                $model->last_name = $userData['last_name'];
                //$model->company_name = $userData['company_name'];
                $model->email = $userData['email'];
                $model->jwt_token = $jwt_token;
                $model->save();
            }
        } catch (Exception $e ) {
            return response()->json(['error'=>'unauthorizedddd', 'message'=>$e->getMessage()], 200);
        }

        // Besides grabbing the jwt token, why do we need this and why are we sending both tokens to the front-end? @john
        
       // $ticket = $user->createToken('DF2B')->accessToken;
        if(Login::getCustomerData($jwt_token)==1){
            return response()->json(['Status'=>'403 Forbidden' , 'Message'=>'Customer account not linked to this account'], 403);
        }
        
         else if(!empty($jwt_token) && Auth::attempt(['email' => request('email'), 'password' => request('password')]) ) {
            $user = Auth::user();
            $profile['id'] =  $user->id;
            $profile['email'] =  $user->email;
            $profile['fist_name'] =  $user->first_name;
            $profile['last_name'] =  $user->last_name;
            $profile['phone_number'] =  $user->phone_number;
            $profile['settings'] =  $user->settings;
            $profile['city'] =  $user->city;
            $profile['company_name'] =  $user->company_name;
            $profile['note'] =  $user->note;
            $token =  $user->createToken('DF2B')->accessToken;
            
            $customerData = Login::getCustomerData($user->jwt_token);
            //$responseData = Login::getUserData($token);
           // $responseData = Login::getUserData($user->jwt_token);
            return response()->json(["profile" => $profile, "token" => $token, "customerData" =>$customerData, ], 200);
        }
        else{
            return response()->json(['error'=>'unauthorized'], 200);
        }
    }


    public function logout(Request $request)
    {
        try 
        {
            Login::doLogout();
        }   catch (Exception $e) {
            return response()->json(['error'=>'logout_failed'], 200);
        }
        
    }
}
