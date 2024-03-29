<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\PublisherRequest;
use App\Helpers\Helper;
use App\Mail\SendRequestMail;
use Exception;
use Mail;

class UserController extends Controller
{    
    //User Can Edit Their Profile Name
    public function edit(Request $request) {
        $request->validate([
            "name" => "required|string",
        ]);
        
        try {
            $user  = auth()->user();
            $user->update(['name'=>$request->name]);

            Helper::createActivity("User", "Update", "$user->email Updated Profile Name.");
            return response()->json([
                "message"=>"Data Updated Successfully.",
            ],200);
        } catch(\Exeception $e) {
            report($e);
            return response()->json([
                "message"=>"Something went wrong!",
            ],500);
        }
    }

    //User Send Request To Become Publisher
    public function sendRequest(Request $request){
        $request->validate([
            'description'=>'required',
        ]);

        try{
            $user = auth()->user();
            $admin = User::where('role',2)->first();

            if($user->publisherRequest) {
                return response()->json([
                    "message" => "Already Applied!"
                ],500);
            }

            $publisher = $user->publisherRequest()->create([
                'name' => $user->name,
                'email' => $user->email,
                'description' => $request->description,
            ]);

            //Send Mail to admin's email that User Request to wants to be a Publisher 
            Mail::to($admin->email)->send(new SendRequestMail($publisher));
            
            Helper::createActivity("User","Send-Request", "$user->email Send Request To Admin.");
            return response()->json([
                'message'=>'Request Sended.',
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                "message"=>"Something went Wrong!",
            ],500);
        }       
    }

    //To Enable/Disable api-token for User 
    public function apiToggle($status){
        if(!in_array($status, ['enable', 'disable'])) {
            return response()->json([
                "message"=>"Invalid Status!",
            ],500);
        }

        $user = auth()->user();
        if($status == "enable"){
            $token = Helper::generateUniqueToken(32, "users", "api_token");
            $user->update(['api_token'=>$token]);
            Helper::createActivity("User", "Create", "$user->email Created Api Token.");
            return response()->json([
                "message" => "Api Token Enable.",
            ],200);
        }
        else{
            $user->update(['api_token'=>null]);
            Helper::createActivity("User", "Delete", "$user->email Delete Api Token.");
            return response()->json([
                "message"=>"Api Token Disable.",
            ],200);
        }
    }
}
