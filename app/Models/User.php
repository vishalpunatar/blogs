<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\PublisherRequest;
use App\Traits\DateTimeTrait;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, DateTimeTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        //'created_at' => 'datetime:Y-m-d D h:i:sA',
        //'updated_at' => 'datetime:Y-m-d D h:i:sA',
    ];

    public function blogs(){
        return $this->hasMany(Blog::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function like(){
        return $this->hasOne(Like::class);
    }

    public function publisherRequest(){
        return $this->hasOne(PublisherRequest::class);
    }

    public function activities(){
        return $this->hasMany(ActivityLog::class);
    }
}
