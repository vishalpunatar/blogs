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
    public function createBlog(Request $request){
        $request->validate([
            'title' => 'required|string|unique:blogs,title',
            'content' => 'required',
            'image' => 'required|image',
        ]);
        
        try{
            $image = Str::random(12).".".$request->image->extension();
            $request->image->storeAs('/public/image',$image);
            
            $blog = auth()->user()->blogs()->create([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $image,    
            ]);
            
            //To Store activity using Helper Function
            Helper::createActivity("Blog", "Create", "Blog(title: $blog->title) Created.");
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
    public function blogData(Blog $blog){
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

    //Edit Blog by Publisher 
    public function editBlog(Request $request, Blog $blog) {
        
        $request->validate([
            'title'=>'required|string',
            'content'=>'required',
            'image'=>'required|image',
        ]);

        try{
            $user = auth()->user();
            if(!$user->blogs->contains($blog)) {
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
            
            Helper::createActivity("Blog", "Update", "$user->email Updated Blog(title: $blog->title).");
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
            if(!$user->blogs->contains($blog)) {
                return response()->json([
                    "message"=>"You don't have authorization to Delete this Blog!",
                ],401);
            }
            else{
                $blog->delete();
            }

            Helper::createActivity("Blog", "Delete", "$user->email Deleted Blog($blog->title).");
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
            $user = auth()->user();
            $comment = $user->comments()->create([
                'blog_id' => $blog->id,
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
    public function addReply(Request $request, Comment $comment){
        $request->validate([
            'comment' => 'required',
        ]);
        
        try {
            $user = auth()->user();
            $reply = $user->comments()->create([
                'blog_id' => $comment->blog_id,
                'parent_id' => $comment->id,
                'comment' => $request->comment,
            ]);
    
            Helper::createActivity("Comment", "Add", "$user->email Reply Added to Comment($comment->comment) on Blog(id: $comment->blog_id).");
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
           
            Helper::createActivity("Like", "Add", "$user->email Like Added on Blog($blog->title).");
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

    //To Delete the Particular Comment
    public function deleteComment(Comment $comment){
        try {
            $comment->delete();
            $comment->where('parent_id',$comment->id)->delete();
            
            Helper::createActivity("Comment", "Delete", "Comment Deleted($comment->comment) of Blog(id: $comment->blog_id).");
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
