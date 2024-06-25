<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    protected $casts = ['areas_of_work' => 'array'];
    protected $guarded=[];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
    public function applications(){
        return $this->hasMany(Application::class);
    }
    
}
