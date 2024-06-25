<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable=['skill'];
    protected $casts=['skill'=>'array'];

    public function User(){
        return $this->belongsTo(User::class);
    }
}
