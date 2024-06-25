<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TargetJobController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('admin/login',[AdminController::class,'login']);
Route::put('admin/enable_job',[JobController::class,'enable']);
Route::put('admin/disable_job',[JobController::class,'disable']);
Route::get('admin/list_all_users',[AdminController::class,'listAllUsers']);
Route::delete('admin/delete_company',[CompanyController::class,'delete']);
Route::delete('admin/delete_user',[UserController::class,'delete']);
Route::get('admin/list_all_approve_requests',[AdminController::class,'list_all_approve_requests']);
Route::get('admin/list_all_jobs',[JobController::class,'listAllJobs']);
Route::get('admin/list_all_companies',[CompanyController::class,'listAllCompanies']);
Route::put('admin/disable_company',[CompanyController::class,'disable']);
Route::put('admin/enable_company',[CompanyController::class,'enable']);
Route::put('admin/disable_user',[UserController::class,'disable']);
Route::put('admin/enable_user',[UserController::class,'enable']);
Route::post('admin/search',[AdminController::class,'search']);
Route::delete('admin/delete_job',[JobController::class,'delete']);


Route::post('user/register',[UserController::class,'register']);
Route::post('user/login',[UserController::class,'login']);
Route::post('user/edit_target_job',[TargetJobController::class,'editTargetJob']);
Route::post('user/add_languages',[LanguageController::class,'addLanguages']);
Route::post('user/edit_languages',[LanguageController::class,'editLanguages']);
Route::delete('user/delete_languages',[LanguageController::class,'deleteLanguages']);
Route::post('user/add_skill',[SkillController::class,'addSkill']);
Route::post('user/edit_skill',[SkillController::class,'editSkill']);
Route::delete('user/delete_skill',[SkillController::class,'deleteSkill']);
Route::post('user/add_experience',[ExperienceController::class,'addExperience']);
Route::post('user/edit_experience',[ExperienceController::class,'editExperience']);
Route::delete('user/delete_experience',[ExperienceController::class,'deleteExperience']);
Route::post('user/add_education',[EducationController::class,'addEducation']);
Route::post('user/edit_education',[EducationController::class,'editEducation']);
Route::delete('user/delete_education',[EducationController::class,'deleteEducation']);
Route::get('user/list_my_jobs',[JobController::class,'list_my_jobs']);
Route::get('user/list_my_languages',[LanguageController::class,'listLanguages']);
Route::get('user/list_my_educations',[EducationController::class,'listEducations']);
Route::get('user/list_my_experiences',[ExperienceController::class,'listExperiences']);
Route::get('user/list_my_skills',[SkillController::class,'listSkills']);
Route::get('user/list_my_target_job',[TargetJobController::class,'listTargetJob']);
Route::get('user/list_compnay_details',[CompanyController::class,'list_compnay_details']);
Route::get('user/apply',[UserController::class,'apply']);
Route::get('user/list_applied_jobs',[UserController::class,'list_applied_jobs']);
Route::get('list_user_details',[UserController::class,'list_user_details']);


Route::post('company/register',[CompanyController::class,'register']);
Route::post('company/login',[CompanyController::class,'login']);
Route::post('company/add_Job', [JobController::class, 'addJob']);
Route::post('company/edit_job', [JobController::class, 'editJob']);
Route::post('company/update_profile',[CompanyController::class,'updateProfile']);
Route::get('company/list_applicants',[CompanyController::class,'list_applicants']);




Route::get('list_job_details',[JobController::class,'listJobDetails']);
Route::get('view_profile',[UserController::class,'viewProfile']);
Route::post('update_profile',[UserController::class,'updateProfile']);
Route::post('search_for_company',[CompanyController::class,'search_for_company']);
Route::post('search_for_job',[JobController::class,'search_for_job']);


