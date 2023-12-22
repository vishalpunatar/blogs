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

class BlogController extends Controller
{
    //Create Blog By Publisher
    public function createBlog(Request $request){
        $request->validate([
            'title' => 'required|string|unique:blogs,title',
            'content' => 'required',
            'image' => 'required|image',
        ]);
        
        try{
            $image = Str::random(12).".".$request->image->extension();
            $request->image->storeAs('/public/image',$image);
            
            $blog = Blog::create([
                'user_id' => Auth()->id(),
                'title' => $request->title,
                'content' => $request->content,
                'image' => $image,    
            ]);
            
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
    public function blogs(Request $request){
        try{
            $search =$request->query('search');
            if($search){
                $blogs = Blog::where([
                    ['title','LIKE',"%$search%"],
                    ['status',1],
                ])->paginate(10);
            }
            else{
                $blogs = Blog::where('status',1)->latest()->paginate(10);
            }
            
            return response()->json([
                'blogs'=>$blogs
            ],200);
        }catch (\Exception $e){
            report($e);
            return response()->json([
                'message'=>"Data Not Found!"
            ],404);
        }
    }

    //Get Perticular Blog-Data
    public function blogData(Blog $blog){
        try{
            $comments = Blog::with('comments.replies')->find($blog->id);
            $likes = $blog->likes; 

            return response()->json([
                'message'=>'Blog Details.',
                'comments'=>$comments,
                'likes'=>$likes,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found!',
            ],404);
        }
    }

    //Edit Blog by Publisher 
    public function editBlog(Request $request, Blog $blog) {
        
        $request->validate([
            'title'=>'required|string',
            'content'=>'required',
            'image'=>'required|image',
        ]);

        try{
            $user = auth()->user();
            $blog = $user->blogs()->find($blog->id);
            if(!$blog) {
                return response()->json([
                    "message"=>"You don't have authorization to Edit this Blog!",
                ],401);
            }

            $image = Str::random(12).".".$request->image->extension();
            $request->image->storeAs('/public/image',$image);

            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->image = $image;
            $blog->save();
            
            return response()->json([
                'message'=>'Blog Updated Successfully.',
                'blog'=>$blog,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something went wrong!'
            ],500);
        }
    }

    //Delete Blog By Publisher
    public function blogDelete(Blog $blog){
        try{
            $user = auth()->user();
            $blog = $user->blogs()->find($blog->id);
            if(!$blog) {
                return response()->json([
                    "message"=>"You don't have authorization to Delete this Blog!",
                ],401);
            }
            else{
                $blog->delete();
            }

            return response()->json([
                'message'=>'Blog Deleted Successfully.',
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }

    //User Add Comment To Blog
    public function addComment(Request $request, Blog $blog){
        $request->validate([
            'comment'=>'required',
        ]);

        try{
            $comment = $blog->comments()->create([
                'user_id' => auth()->id(),
                'comment' => $request->comment,
            ]);
            
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
    public function addReply(Request $request, Comment $comment){
        $request->validate([
            'comment' => 'required',
        ]);
        
        try {
            $reply = $comment->create([
                'user_id' => auth()->id(),
                'blog_id' => $comment->blog_id,
                'parent_id' => $comment->id,
                'comment' => $request->comment,
            ]);
    
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
            if($blog->likes->where('user_id',auth()->id())->first()){    
                return response()->json([
                    'message'=>'Already Liked!',
                ],500);    
            }
            else{
                $like = $blog->likes()->create([
                    'user_id' => auth()->id(),
                    'like' => 1,
                ]);    
            }
           
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

    //Super-admin and Publisher Can View Blog Comment's 
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

    //Super-admin and Publisher Can View Blog Like's
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
