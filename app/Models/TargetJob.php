<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetJob extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $casts = [
        'job_type' => 'array',
        'Job_roles' => 'array',
        'work_cities' => 'array',
    ];

    

    public function User(){
        return $this->belongsTo(User::class);
    }
}
