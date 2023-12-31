<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Exception;

class PublisherController extends Controller
{
    //Publisher Can Get His Blog's
    public function myBlog(){
        try{
            $user = auth()->user();
            $search = request()->query('search');

            $blogs = $user->blogs()->when($search, function ($query) use ($search){
                $query->where('title','LIKE',"%$search%");
            })->orderBy('created_at','desc')
            ->paginate(10);

            return response()->json([
                'blogs'=>$blogs,
            ],200);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something went Wrong!',
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
            if(!$user->blogs->find($blog)) {
                return response()->json([
                    "message"=>"You don't have authorization to Edit this Blog!",
                ],403);
            }

            $name = Helper::generateUniqueToken(12, "blogs", "image");
            $image = $name.".".$request->image->extension();
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
            if(!$user->blogs->find($blog)) {
                return response()->json([
                    "message"=>"You don't have authorization to Delete this Blog!",
                ],403);
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

    //Publisher Delete Comments of their Own Blogs
    public function commentDelete(Blog $blog, Comment $comment){
        try {
            $user = auth()->user();
            if(!$user->blogs->find($blog)){
                return response()->json([
                    "message"=>"You don't have authorization to Delete Comments of this Blog!",
                ],403); 
            }
            else{
                $comment->delete();
                $comment->where('parent_id',$comment->id)->delete();
                Helper::createActivity("Comment", "Delete", "$user->email Deleted Comment($comment->comment).");
                return response()->json([
                    'message'=>'Comment Deleted Successfully',
                ],200);
            }
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }
}
