<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\PublisherRequest;
use App\Mail\RequestAcceptedMail;
use Exception;
use Mail;

class SuperadminController extends Controller
{
    //Get All User's Data
    public function userList(){
        try {
            $search = request()->query('search');

            $users = User::when($search, function ($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%");
            })->orderBy('created_at','desc')
            ->paginate(10);
            
            return response()->json([
                "users" => $users,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "No User Found!",
            ],404);
        }
    }

    //Get All Blog's Data
    public function blogList(){
        try {
            $search =request()->query('search');

            $blogs = Blog::when($search, function ($query) use ($search){
                $query->where('title','LIKE',"%$search%");
            })->orderBy('created_at','desc')
            ->paginate(10);
            
            return response()->json([
                "blogs" => $blogs,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "No Data Found!",
            ],404);
        }
    }

    //Get All Publisher's Data
    public function publisherList(){
        try {
            $search = request()->query('search');
            
            $publishers = User::where('role',1)
            ->where(function ($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%");
            })->orderBy('created_at','desc')
            ->paginate(10);           

            return response()->json([
                "publishers" => $publishers,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "No Data Found!",
            ],404);
        }
    }

    //Get All Blog's Request
    public function blogRequestList(){
        try {
            $blogs = Blog::where("status", 0)->latest()->paginate(10);
            return response()->json([
                "blogs" => $blogs,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "No Data Found!",
            ],404);
        }
    }

    //Super-admin Approved Blog's Pending Request
    public function blogApproval(Blog $blog){
        try {
            if($blog->status == 1){
                return response()->json([
                    "message"=>"Already Approved!",
                ],500);
            }
            else{
                $blog->update(["status" => 1]);
            }

            return response()->json([
                "message" => "Approved.",
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }

    //Get All User's Request Who Wants To Become Publisher
    public function publisherRequestList(){
        try {
            $publisherRequests = PublisherRequest::where("req_approval", 0)->latest()->paginate(10);
            
            return response()->json([
                "publisherRequests" => $publisherRequests,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Record Not Found!",
            ],404);
        }
    }

    //Super-admin Approved User's Pending Request
    public function publisherApproval(User $user){
        try{
            $userRequest = PublisherRequest::where('user_id',$user->id)->first();
            
            if($userRequest->req_approval == 1){
                return response()->json([
                    "message"=>"Already Approved!",
                ],500);
            }
            else{
                $user->update(['role' => 1]);

                //Send Mail to User That his Request has been Accepted 
                Mail::to($user->email)->send(new RequestAcceptedMail($user));
                $userRequest->update(['req_approval'=>1]);
            }

            return response()->json([
                "message" => "Approved.",
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }

    //Delete Perticular User by Super-admin
    public function userDelete(User $user){
        try {
            $user->delete();
            return response()->json([
                "message" => "User Deleted Successfully.",
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }

    //Delete Perticular Blog by Super-admin
    public function blogDelete(Blog $blog){
        try {
            $blog->delete();
            return response()->json([
                "message" => "Blog Deleted Successfully.",
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went Wrong!",
            ],500);
        }
    }

    //Edit User by Super-admin
    public function editUser(Request $request, User $user){
        $request->validate([
            "name" => "required",
            "role" => "required",
            "status" => "required",
        ]);

        try {
            $user->name = $request->name;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->save();

            return response()->json([
                "message" => "User Updated Successfully.",
                "user" => $user,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went Wrong!",
            ],500);
        }
    }
    
    //Edit Blog by Super-admin
    public function editBlog(Request $request, Blog $blog){   
        $request->validate([
            "title" => "required|string",
            "content" => "required",
            "image" => "required|image",
            "status" => "required",
        ]);

        try {
            $image = Str::random(12). "." . $request->image->extension();
            $request->image->storeAs("/public/image", $image);

            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->image = $image;
            $blog->status = $request->status;
            $blog->save();

            return response()->json([
                "message" => "Blog Updated Successfully.",
                "blog" => $blog,
            ],200);
        }catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }

    //Super-admin Can Manage User's Role & Status
    public function manageUser(Request $request, User $user){
        $request->validate([
            "role"=>"required",
            "status"=>"required",
        ]);

        try {
            $user->update(['role'=>$request->role,'status'=>$request->status]);
            return response()->json([
                'message'=>'User Updated Successfully.'
            ],200); 
        } catch (Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }
}

