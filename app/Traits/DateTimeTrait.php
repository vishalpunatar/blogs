<?php
namespace App\Traits;

use Carbon\Carbon;

trait DateTimeTrait 
{
    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }

    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
?>