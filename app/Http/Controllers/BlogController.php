<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'title' => 'required',
            'content' => 'required',
            'image' => 'required|image',
        ]);
        
        try{
            $img = time().".".$request->image->extension();
            $request->image->storeAs('/public/image',$img);
            
            $blog = new Blog;
            $blog->user_id = Auth()->id();
            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->image = $img;
            $blog->save();

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
    public function blog(Request $request, Blog $blog){
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
                'message'=>"Not Found."
            ],404);
        }
    }

    //Get Perticular Blog-Data
    public function blogData(Blog $blog){
        try{
            $blog = Blog::where('id',$blog->id)->where('status',1)->first();
            if(!$blog){
                return response()->json([
                    "message"=>"Record Not Found!",
                ],404);
            }
            $totalcomments = $blog->comments()->get();
            $comments = $totalcomments->whereNull('parent_id');
            $likes = $blog->likes()->get();
            $reply = $totalcomments->whereNotNull('parent_id'); 


            return response()->json([
                'message'=>'Blog Details',
                'blog'=>$blog,
                'totalcomments'=>$totalcomments->count(),
                'comments'=>$comments,
                'totalreply'=>$reply->count(),
                'reply'=>$reply,
                'totallikes'=>$likes->count(),
                'likes'=>$likes,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'No Data Found',
            ],404);
        }
    }

    //Edit Blog by Publisher 
    public function editBlog(Request $request, Blog $blog) {
        
        $request->validate([
            'title'=>'required',
            'content'=>'required',
            'image'=>'required|image',
        ]);

        try{    
            $user = auth()->user();
            $blog = $user->blogs()->find($blog->id);
            if(!$blog) {
                return response()->json([
                    "message"=>"You don't have authorization to Edit this Blog!",
                ],404);
            }

            $img = time().".".$request->image->extension();
            $request->image->storeAs('/public/image',$img);

            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->image = $img;
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
            $blog = $user->blogs()->findOrFail($blog->id)->delete();

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
        try{
            $request->validate([
                'comment'=>'required',
            ]);
            
            $blog = Blog::findOrFail($blog->id);
            
            $comment = new Comment;
            $comment->user_id = auth()->id();
            $comment->blog_id = $blog->id;
            $comment->comment = $request->comment;
            $comment->save();
    
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

        $comment = Comment::where('id',$comment->id)->first();

        $reply = new Comment;
        $reply->user_id = auth()->id();
        $reply->blog_id = $comment->blog_id;
        $reply->parent_id = $comment->id;
        $reply->comment = $request->comment;  
        $reply->save();

        return response()->json([
            'message'=>'Reply Added.',
            'reply'=>$reply,
        ],200);
    }

    //Add Like To Particular Blog   
    public function addLike(Blog $blog){
        try{
            $blog = Blog::findOrFail($blog->id);
            $user = auth()->user();
            
            if($blog->likes->where('user_id',$user->id)->first()){    
                return response()->json([
                    'message'=>'Already Liked.',
                ],500);    
            }
            else{
                $like = $blog->likes()->create([
                    'user_id' => $user->id,
                    'blog_id' => $blog->id,
                    'like' => 1,
                ]);    
            }
           
            return response()->json([
                'message'=>'Like Added.',
                'like'=>$like,
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
            $totalcomments = Blog::findOrFail($blog->id)->comments()->get();
            $comments = $totalcomments->whereNull('parent_id');  
            $totalcomments->flatMap->replies;

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
            $blog = Blog::findOrFail($blog->id);
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
            $like = Blog::findOrFail($blog->id)->likes()->get();
            
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
