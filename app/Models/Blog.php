<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Traits\DateTimeTrait;

class Blog extends Model
{
    use HasFactory, DatetimeTrait;

    public $table = 'blogs';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(){
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function replies(){
        return $this->hasMany(Comment::class,'parent_id');
    }
}
