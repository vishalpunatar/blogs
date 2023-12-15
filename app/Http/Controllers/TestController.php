<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Blog;

class AuthController extends Controller
{

    // public function index(){
    //     return view('signup');
    // }
    
    public function loadlogin(){
        if(!Auth::user()){
            return redirect('/home');
        }

        //$token = auth()->user()->createToken('api token')->accessToken;
        $route = $this->redirectTo();
        return ($route);//->with(['blog'=>$blog],200);
    }

    public function store(Request $request, User $user){
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8'
        ]);
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $token = $user->createToken('userToken')->accessToken;
        //return response(['user' => $user, 'token' => $token]);
        return redirect('/login');
    }

    public function loginpage(){
        return view('login');
    }

    public function login(Request $request, Blog $blog){
        // $request->validate([
        //     'email' => 'required|unique:users|email',
        //     'password' => 'required|min:8'
        // ]);
        
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $route = $this->redirectTo();
            $token = auth()->user()->createToken('api token')->accessToken;
            return redirect($route)->with(['token'=>$token],200);
        }

        return redirect($this->loadlogin());
    }

    public function redirectTo(){
        
        $redirect = '';

        if(Auth::user() && Auth::user()->role == 2){
            return $redirect = '/super-admin/dashboard';
        }
        elseif(Auth::user() && Auth::user()->role == 1){
            return $redirect = '/publisher/dashboard';
        }
        elseif(Auth::user() && Auth::user()->role == 0){
            return $redirect = '/user/dashboard';
        }
        else{
            return redirect('/home');
        }

        return $redirect;
    }
    
}
