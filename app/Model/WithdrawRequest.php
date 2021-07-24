<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    public function seller(){
        return $this->belongsTo(Seller::class,'seller_id');
    }
}
