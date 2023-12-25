<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTimeTrait;

class ResetPassword extends Model
{
    use HasFactory, DateTimeTrait;

    public $table = 'password_reset_tokens';
    public $timestamps = false;
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];
}
