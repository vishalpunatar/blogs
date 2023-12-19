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
    public function blog(Request $request){
        try{
            $title =$request->input('title');
            if($title){
                $blog = Blog::where('title','LIKE',"%$title%")
                ->where('status',1)
                ->paginate(10);
            }
            else{
                $blog = Blog::where('status',1)->latest()->paginate(10);
            }
            
            return response()->json([
                'blog'=>$blog
            ],200);
        }catch (\Exception $e){
            report($e);
            return response()->json([
                'message'=>"Not Found!"
            ],404);
        }
    }

    //Get Perticular Blog-Data
    public function blogData(Blog $blog){
        try{
            $totalcomments = $blog->comments;
            $comments = $totalcomments->whereNull('parent_id');
            $likes = $blog->likes;
            $reply = $totalcomments->whereNotNull('parent_id'); 

            return response()->json([
                'message'=>'Blog Details.',
                 'totalcomments'=>$comments->count(),
                 'totalreply'=>$reply->count(),
                 'totallikes'=>$likes->count(),
                 'blog'=>$blog,
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
            $totalcomments = $blog->comments;  
            $totalcomments->flatMap->replies;

            //it  return's Only Comments of Perticular Blog
            $comments = $totalcomments->whereNull('parent_id');

            return response()->json([
                'totalcomment'=>$totalcomments->count(),
                'comments'=>$comments,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found!'
            ],404);
        }
    }

    //Super-admin and Publisher Can View Blog Replies
    public function showReply(Blog $blog){
        try {
            $reply = $blog->comments->whereNotNull('parent_id');
                
            return response()->json([
                'totalreply'=>$reply->count(),
                'reply'=>$reply,
            ],200);   
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }  
    }

    //Super-admin and Publisher Can View Blog Like's
    public function showLike(Blog $blog){
        try{
            $like = $blog->likes;
            
            return response()->json([
                'totallikes'=>$like->count(),
                'like'=>$like,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found!',
            ],404);
        }
    }
}
