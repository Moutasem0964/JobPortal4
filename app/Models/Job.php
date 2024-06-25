<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_title',
        'employment',
        'gender',
        'min_age',
        'max_age',
        'educational_level',
        'career_level',
        'languages',
        'number_of_vacancies',
        'type_of_employment',
        'city',
        'address',
        'min_salary',
        'max_salary',
        'job_description',
        'cover_letter_required',
        'experience_years'
    ];
    protected $casts = [
        'languages' => 'array',
        'type_of_employment' => 'array',
    ];
    

    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function applications(){
        return $this->hasMany(Application::class);
    }
}
