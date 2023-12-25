<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\DateTimeTrait;

class PublisherRequest extends Model
{
    use HasFactory, DateTimeTrait;
    public $table = 'publisher_requests';
    protected $fillable =[
        'user_id',
        'name',
        'email',
        'description',
        'req_approval',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
