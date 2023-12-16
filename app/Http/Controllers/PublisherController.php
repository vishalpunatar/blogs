<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;

class PublisherController extends Controller
{
    //Publisher Can Get His Blog's
    public function myBlog(Request $request){
        try{
            $user = auth()->user();
            $title =$request->input('title');
            if($title){
                $blog = $user->blogs()->where('title','LIKE',"$title")->paginate(10);
            }
            else{
                $blog = $user->blogs()->paginate(10);
            }

            return response()->json([
                'totalblog'=>$blog->count(),
                'blog'=>$blog,
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
