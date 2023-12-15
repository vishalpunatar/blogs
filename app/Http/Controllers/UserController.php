<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Blog;
use App\Models\PublisherRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Mail\SendRequestMail;
use Mail;
use Exception;

class UserController extends Controller
{
    //User Can Edit Their Profile
    public function edit(Request $request) {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
        ]);
        
        try {
            $user = auth()->user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $user->password;
            $user->save();

            return response()->json([
                "message"=>"Data Updated Successfully.",
                "user"=>$user,
            ],200);
        } catch(\Exeception $e) {
            report($e);
            return response()->json([
                "message"=>"Something went wrong!",
            ],500);
        }
    }

    //User Send Request To Become Publisher
    public function publisherRequest(Request $request, PublisherRequest $publisher){
        $request->validate([
            'description'=>'required',
        ]);

        try{
            $user = auth()->user();
            $admin = User::where('role',2)->first();
            $token = Str::random(32);

            if($user->publisherRequest) {
                return response()->json([
                    "message" => "Already applied."
                ],500);
            }

            $publisher = $user->publisherRequest()->create([
                'name' => $user->name,
                'email' => $user->email,
                'description' => $request->description,
                'token' => $token,
            ]);

            //Send Mail to admin's email that User Request to wants to be a Publisher 
            Mail::to($admin->email)->send(new SendRequestMail($publisher,$token,$user));
            
            return response()->json([
                'message'=>'Request Sended.',
                'publisher'=>$publisher,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                "message"=>"Something went Wrong!",
            ],500);
        }       
    }
}
