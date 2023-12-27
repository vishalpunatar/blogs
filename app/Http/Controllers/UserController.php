<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\PublisherRequest;
use App\Mail\SendRequestMail;
use Mail;
use Exception;
use App\Traits\DateTimeTrait;
use App\Helpers\Helper;

class UserController extends Controller
{
    use DateTimeTrait;
    
    //User Can Edit Their Profile
    public function edit(Request $request) {
        $request->validate([
            "name" => "required|string",
        ]);
        
        try {
            $user  = auth()->user();
            $user->update(['name'=>$request->name]);

            Helper::createActivity("User", "Update", "$user->email Updated Profile.");
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
    public function publisherRequest(Request $request){
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
}
