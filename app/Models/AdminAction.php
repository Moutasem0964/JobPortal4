<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    use HasFactory;
    public function object(){
        return $this->morphTo();
    }
    public function Admin(){
        return $this->belongsTo(Admin::class);
    }
}
