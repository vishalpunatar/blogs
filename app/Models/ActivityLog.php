<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTimeTrait;
use App\Models\User;

class ActivityLog extends Model
{
    use HasFactory, DateTimeTrait;

    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id',
        'type',
        'on',
        'action',
        'description',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}

