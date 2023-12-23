<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $blogs = $search?$user->blogs()->where('title','LIKE',"%$search%")->paginate(10):$user->blogs()->paginate(10);

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

    // //Get Perticular Own Blog-Data
    // public function PubBlogData(Blog $blog){
    //     try{
    //         $user = auth()->user();
    //         $blog = $user->blogs()->findOrFail($blog->id);
            
    //         $comments = $blog->comments()->get();
    //         $totalcomments = count($comments);
    //         $likes = $blog->likes()->get();
    //         $totallikes = count($likes);

    //         return response()->json([
    //             'message'=>'Blog Details',
    //             'blog'=>$blog,
    //             'totalcomments'=>$totalcomments,
    //             'comments'=>$comments,
    //             'totallikes'=>$totallikes,
    //             'likes'=>$likes,
    //         ],200);
    //     }catch(\Exception $e){
    //         report($e);
    //         return response()->json([
    //             'message'=>'No Data Found',
    //         ],404);
    //     }
    // }
}
