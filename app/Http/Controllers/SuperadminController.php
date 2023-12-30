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
use App\Helpers\Helper;
use Exception;
use Mail;

class SuperadminController extends Controller
{
    //Get All User's Data
    public function users(){
        try {
            $search = request()->query('search');

            $users = User::when($search, function ($query) use ($search){
                $query->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
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
    
    //To get Particular User Detail.
    public function userShow(User $user){
        try {
            return response()->json([
                'message'=>'User Details',
                'user'=>$user,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }

    //Get All Blog's Data
    public function blogs(){
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
    public function publishers(){
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
    public function blogRequests(){
        try {
            $blogs = Blog::where('status', 0)->latest()->paginate(10);
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
            // return Blog::findOrFail($blog->id);
            if($blog->status == 1){
                return response()->json([
                    "message"=>"Already Approved!",
                ],500);
            }
            else{
                $blog->update(["status" => 1]);
            }

            Helper::createActivity("Blog", "Blog-Approve", "Approved Blog(title: $blog->title).");
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
    public function userRequests(){
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
    public function userApproval(User $user){
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
            
            Helper::createActivity("User", "Request-Approve", "Approved User($user->email).");
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

            Helper::createActivity("User", "Delete", "Deleted User($user->email).");
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

            Helper::createActivity("Blog", "Delete", "Deleted Blog(title: $blog->title).");
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

            Helper::createActivity("User", "Update", "Updated User($user->email).");
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
            $name = Helper::generateUniqueToken(12, "blogs", "image");
            $image = $name.".".$request->image->extension();
            $request->image->storeAs("/public/image", $image);

            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->image = $image;
            $blog->status = $request->status;
            $blog->save();

            Helper::createActivity("Blog", "Update", "Update Blog (title:$blog->title).");
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

            Helper::createActivity("User", "Update", "Updated User($user->email).");
            return response()->json([
                'message'=>'User Updated Successfully.'
            ],200); 
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }


    //To Delete the Particular Comment
    public function deleteComment(Blog $blog, Comment $comment){
        try {
            $comment->delete();
            $comment->where('parent_id',$comment->id)->delete();
            Helper::createActivity("Comment", "Delete", "Comment Deleted($comment->comment) of Blog(title: $blog->title).");
            return response()->json([
                "message"=>"Comment Deleted Successfully.",
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message"=>"Something Went Wrong!",
            ],500);
        }
    } 
}

