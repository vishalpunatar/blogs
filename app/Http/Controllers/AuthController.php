<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\ResetPassword;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use App\Helpers\Helper;
use Mail;
use Exception;

class AuthController extends Controller
{
    //To Store User data
    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:15|confirmed',
        ]);

        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);    
            
            $token = $user->createToken('userToken')->accessToken;
            
            return response()->json([
                'message'=>'Sign-up Successfully.',
                'token' => $token,
            ],200);
        } catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something went Wrong!',
            ],500);
        }   
    }

    //To Login Auth User
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:15',
        ]);
        
        try{
            $user = User::where('email',$request->email)->firstOrFail();
            if($user->status == 0){
                  // Revoke all tokens
                foreach ($user->tokens as $token) {
                    $token->revoke();
                }            

                return response()->json([
                    'message'=>'You are Temporary Banned!',
                ],403);
            }

            if(auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $token = auth()->user()->createToken('userToken')->accessToken;
                Helper::createActivity("User", "Login", "$request->email has been Login.");
                return response()->json([
                    'message'=>'Login Successfull.',
                    'token'=>$token,
                ],200);
            }else{
                return response()->json([
                    'message'=>'Incorrect Email or Password!',
                ],401);
            }
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!'
            ],500);
        }
    }

    //Get Authenticate User Profile
    public function profileshow() {
        try {
            $user = auth()->user();
            return response()->json([
                'user'=>$user,
            ],200);
        } catch(\Exeception $e) {
            report($e);
            return response()->json([
                'message'=> 'Something went wrong!',
            ],500);
        }
    }

    //To Change User Existing Password
    public function changePassword(Request $request){
        $request->validate([
            'oldpassword'=>'required',
            'password'=>'required|min:8|max:15|confirmed',
        ]);

        try {
            $user = auth()->user();
            if(!Hash::check($request->oldpassword, $user->password)){
                return response()->json([
                    "message" =>"Old Password Was Incorrect!",
                ],401);
            }
            else{
                $user->update(['password' => bcrypt($request->password)]);
                Helper::createActivity("User", "Update", "$user->email Changed Password.");
                return response()->json([
                    "message"=>"Password Changed Successfully.",
                ],200);
            }

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message"=>"Something Went Wrong!",
            ],500);            
        }
    }
    
    //To Forget Password through Email 
    public function forgetPassword(Request $request){
        $request->validate([
            'email'=>'required|email|exists:users,email',
        ]);

        try {
            $token = Helper::generateUniqueToken(32,"password_reset_tokens","token");   

            $data = ResetPassword::create([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            //Send mail on User's Requested Email if Email Exists 
            Mail::to($request->email)->send(new ResetPasswordMail($token));
            
            return response()->json([
                'message'=>'Mail Sended Successfully.',
            ],200);  
        } catch (\Exception $e) {
             report($e);
             return response()->json([
                 'message'=>'Something Went Wrong!',
             ],500);
        }
    }
    
    //To Reset Password via Email
    public function resetPassword(Request $request, $token){
        try {
            $request->validate([
                'password' => 'required|min:8|max:15|confirmed',
            ]);
        
            // Find the reset password token from the database
            $resetToken = ResetPassword::where('token',$token)->first();
            if(!$resetToken){
                return response()->json([
                    'message'=>'Invalid Token!',
                ],500);
            }

            // Find the user with the email associated with the token
            $user = User::where('email',$resetToken->email)->first();
            if(!$user){
                return response()->json([
                    'message'=>'Email Not Found!',
                ],404);
            }

            // Update the user's password with the new one
            $user->update(['password'=>bcrypt($request->password)]);

            // Delete the used reset password token
            $resetToken->where('token',$token)->delete();
            
            return response()->json([
                'message'=>'Password Updated Successfully.',
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }        
    }

    //To Logout 
    public function logout(){
        try{
            auth()->user()->token()->revoke();
            return response()->json([
                'message'=>'Logged Out Successfully.',
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }
}
