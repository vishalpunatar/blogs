<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Models\User;
use Exception;

class ActivityLogController extends Controller
{
    //Auth User Show it's own Activities
    public function showActivity(){
        try {
            $user = auth()->user();
            $activities = $user->activities()->paginate(10);

            return response()->json([
                'activity'=>$activities,
            ],200);
        } catch (\Exception $e){
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }
    }

    //Super-admin Show All Users Activities
    public function allActivity(){
        try {
            $activities = ActivityLog::orderBy('created_at','desc')->paginate(10);
            return response()->json([
                "activity"=>$activities,
            ],200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message'=>'Something Went Wrong!',
            ],500);
        }

    }
}
