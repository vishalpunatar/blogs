<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Blog;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\PublisherRequest;
use App\Models\ActivityLog;
use App\Helpers\Helper;

class BlogController extends Controller
{
    //Create Blog By Publisher
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|unique:blogs,title',
            'content' => 'required',
            'image' => 'required|image',
        ]);
        
        try{
            $name = Helper::generateUniqueToken(12, "blogs", "image");
            $image = $name.".".$request->image->extension();
            $request->image->storeAs('/public/image',$image);
            
            $user = auth()->user();
            $blog = $user->blogs()->create([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $image,    
            ]);
            
            //To Store activity using Helper Function
            Helper::createActivity("Blog", "Create", "Publisher($user->email) has been Created Blog(title: $blog->title).");
            return response()->json([
                'message'=>'Blog Added Successfully.',
                'blog'=>$blog,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something went Wrong!',
            ],500);
        }
    }

    //Get The Latest Blog Which are Approved By Super-admin
    public function blogs(){
        try{
            $search =request()->query('search');
            $availableBlogs = Blog::where('status',1);

            $blogs = $availableBlogs->when($search, function($query) use($search){
                $query->where('title','LIKE',"%$search%");
            })->orderBy('created_at','desc')
            ->paginate(10);
                        
            return response()->json([
                'blogs'=>$blogs,
            ],200);
        }catch (\Exception $e){
            report($e);
            return response()->json([
                'message'=>"Data Not Found!",
            ],404);
        }
    }

    //Get Perticular Blog-Data
    public function show(Blog $blog){
        try{
            $blog = $blog->load('comments.replies','likes'); 

            if($blog->status == 1){
                return response()->json([
                    "message"=>"Blog Details.",
                    "blog"=>$blog,
                ],200);
            }
            else{
                return response()->json([
                    "message"=>"No Data Found!",
                ],404);
            }
        }catch(\Exception $e){
            report($e);
            return response()->json([
                "message"=>"Something Went Wrong!",
            ],500);
        }
    }

    //User Add Comment To Blog
    public function addComment(Blog $blog, Request $request){
        $request->validate([
            'comment'=>'required',
        ]);
        
        try{
            $user = auth()->user();
            $comment = $blog->comments()->create([
                'user_id' => $user->id,
                'comment' => $request->comment,
            ]);

            Helper::createActivity("Comment","Create","$user->email Comment($comment->comment) Added on Blog(title: $blog->title).");
            return response()->json([
                'message'=>'Comment Added.',
                'comment'=>$comment,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something  Went Wrong!',
            ],500);
        }
    }

    //Add Comment Reply
    public function addReply(Request $request, Blog $blog, Comment $comment){
        $request->validate([
            'comment' => 'required',
        ]);
        
        try {
            $user = auth()->user();
            $reply = $user->comments()->create([
                'blog_id' => $blog->id,
                'parent_id' => $comment->id,
                'comment' => $request->comment,
            ]);
    
            Helper::createActivity("Comment", "Add", "$user->email Reply Added to Comment($comment->comment) on Blog(title: $blog->title).");
            return response()->json([
                'message'=>'Reply Added.',
                'reply'=>$reply,
            ],200);   
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something Went Wrong!",
            ],500);
        }
    }

    //Add Like To Particular Blog   
    public function addLike(Blog $blog){
        try{
            $user = auth()->user();
            if($blog->likes->where('user_id',$user->id)->first()){    
                return response()->json([
                    'message'=>'Already Liked!',
                ],500);    
            }
            else{
                $like = $user->like()->create([
                    'blog_id' => $blog->id,
                    'like' => 1,
                ]);    
            }
           
            Helper::createActivity("Like", "Add", "$user->email Like Added on Blog(title: $blog->title).");
            return response()->json([
                'message'=>'Like Added.',
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went wrong!',
            ],500);
        }
    }

    //User Can View Blog Comment's 
    public function showComment(Blog $blog){
        try{
            //it return's Comments including Comment Replies of Perticular Blog 
            $comments = $blog->comments;  
            $comments->flatMap->replies;

            return response()->json([
                'comments'=>$comments,
                
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found!'
            ],404);
        }
    }

    //User Can View Blog Like's
    public function showLike(Blog $blog){
        try{
            $likes = $blog->likes;
            
            return response()->json([
                'likes'=>$likes,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found!',
            ],404);
        }
    }
}

//$requests->update(['req_approval' => 1]);
        //$requests->user->update(['role' => 1]);
