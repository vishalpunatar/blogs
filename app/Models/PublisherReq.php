<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PublisherReq extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'name',
        'email',
        'description',
        'req_approval',
        'token',
    ];

    //protected $table =  'PublisherReqs';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
