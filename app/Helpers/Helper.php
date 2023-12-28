<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;

class Helper
{
    //it Define User-Type
    public static function userType(){
        $userType = "";
        if (auth()->user()->role == 0){
            return $userType = "User";
        } 
        elseif (auth()->user()->role == 1){
            return $userType = "Publisher";
        }
        else{
            return $userType = "Super-Admin";
        }
    }

    //to Create An Activity 
    public static function createActivity($on, $action, $description){
        $type = self::userType();

        $data = auth()->user()->activities()->create([
            'type' => $type,
            'on' => $on,
            'action' => $action,
            'description' => $description,
        ]);
    }

    public static function updateActivity($on, $action, $description){
        $type = self::userType();
        $time = Carbon::now();

        $data = auth()->user()->activities()->create([
            'type' => $type,
            'on' => $on,
            'action' => $action,
            'description' =>$description 
        ]);
    }

    public static function generateUniqueToken($size = 32, $table = 'users', $column = 'api_token'){
        $token = Str::random($size);
        if($table && DB::table($table)->where($column,$token)->count()){
            Helper::generateUniqueToken($size, $table, $column);
        }
        return $token;
    } 
}

?>
