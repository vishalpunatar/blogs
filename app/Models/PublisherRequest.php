<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class PublisherRequest extends Model
{
    use HasFactory;
    public $table = 'publisher_requests';
    protected $fillable =[
        'user_id',
        'name',
        'email',
        'description',
        'req_approval',
        'token',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
