<?php

namespace App\Http\Controllers;

use App\Models\AdminAction;
use App\Models\Application;
use App\Models\TargetJob;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Notifications\AccountDisabled;
use App\Notifications\AccountEnabled;
use App\Notifications\NewApply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'home_addres' => 'required|max:255',
            'phone_number' => 'required|numeric',
            'city' => 'required|max:255',
            'nationality' => 'required|max:255',
            'birthday' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'Experience_years' => 'required|numeric',
            'Education' => 'required|max:255',
            'Joblevel' => 'required|max:255',
            'job_time' => 'required|array',
            'Job_roles' => 'required|array',
            'work_cities' => 'required|array',
            'current_job_status' => 'required|max:255',
        ]);

        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->home_addres = $request->input('home_addres');
        $user->phone_number = $request->input('phone_number');
        $user->city = $request->input('city');
        $user->nationality = $request->input('nationality');
        $user->birthday = $request->input('birthday');
        $user->gender = $request->input('gender');
        $user->save();
        $target_job = new TargetJob();
        $target_job->user_id = $user->id;
        $target_job->Experience_years = $request->input('Experience_years');
        $target_job->Education = $request->input('Education');
        $target_job->Joblevel = $request->input('Joblevel');
        $target_job->job_type = $request->input('job_time');
        $target_job->Job_roles = $request->input('Job_roles');
        $target_job->work_cities = $request->input('work_cities');
        $target_job->current_job_status = $request->input('current_job_status');
        $target_job->save();

        $token = $user->createToken('userToken', ['user'])->plainTextToken;
        return response()->json([
            'message' => 'Welcome',
            'token' => $token,
        ], 200);
    }
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);
        $user = User::where('email', $request->input('email'))->first();
        if ($user && Hash::check($request->input('password'), $user->password)) {
            $token = $user->createToken('userToken', ['user'])->plainTextToken;
            return response()->json([
                'message' => 'welcome',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'wrong credential'
            ], 401);
        }
    }
    public function delete(Request $request)
    {
        if ($admin = Auth::guard('admin')->user()) {
            $user = User::where('id', $request->header('id'))->first();
            if ($user) {
                $user->delete();
                $admin->AdminActions()->create([
                    'action_type' => 'delete a user account',
                    'object_type' => 'user',
                    'object_id' => $user->id,
                ]);
                return response()->json([
                    'message' => 'deleted successfuly'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function disable(Request $request)
    {
        /** @var APP\Models\Admin $admin */
        if ($admin = Auth::guard('admin')->user()) {
            $user = User::where('id', $request->header('id'))->first();
            if ($user) {
                $user->status = 0;
                $user->save();
                $admin->AdminActions()->create([
                    'action_type' => 'disable a user account',
                    'object_type' => 'User',
                    'object_id' => $user->id
                ]);
                $user->notify(new AccountDisabled());
                return response()->json([
                    'message' => 'disabled successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant find user'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthentictaed'
            ], 401);
        }
    }
    public function enable(Request $request)
    {
        if ($admin = Auth::guard('admin')->user()) {
            $user = User::where('id', $request->header('id'))->first();
            if ($user) {
                $user->status = 1;
                $user->save();
                $admin->AdminActions()->create([
                    'action_type' => 'enable a user account',
                    'object_type' => 'User',
                    'object_id' => $user->id,
                ]);
                $user->notify(new AccountEnabled());
                return response()->json([
                    'message' => 'enabled successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant find user'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthentictaed'
            ], 401);
        }
    }
    public function viewProfile()
    {

        if ($user = Auth::guard('user')->user()) {
            return response()->json([
                'data' => $user,
            ], 200);
        } elseif ($comapny = Auth::guard('company')->user()) {

            return response()->json([
                'data' => $comapny,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'first_name' => 'sometimes|required|max:255',
                'last_name' => 'sometimes|required|max:255',
                'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|min:8',
                'home_addres' => 'sometimes|required|max:255',
                'phone_number' => 'sometimes|required|numeric',
                'city' => 'sometimes|required|max:255',
                'nationality' => 'sometimes|required|max:255',
                'birthday' => 'sometimes|required|date',
                'gender' => 'sometimes|required|in:Male,Female,Other',
                'cover_letter' => 'sometimes|nullable|max:5000',
                'photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ]);
            
            foreach ($validatedData as $key => $value) {
                if ($request->has($key)) {
                    if ($key == 'password') {
                        $user->$key = Hash::make($value);
                    } else {
                        $user->$key = $value;
                    }
                }
            }
            if ($request->hasFile('photo')) {
                $profilePhotoPath = $request->file('photo')->store('profile_photos', 'public');
                $user->photo = $profilePhotoPath;
            }

            $user->save();

            return response()->json(['message' => 'Profile updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    public function apply(Request $request)
    {
        /** @var App\Models\User $user */
        $user = Auth::guard('user')->user();
        if ($user) {
            $job = Job::find($request->header('id'));
            if ($job && $job->status == 1) {
                if ($user->educations()->get()->isEmpty() || $user->experiences()->get()->isEmpty() || $user->languages()->get()->isEmpty()) {
                    return response()->json([
                        'message' => 'please complete your qualifications'
                    ], 403);
                } elseif (!$user->cover_letter   && $job->cover_letter_required) {
                    return response()->json([
                        'message' => 'please enter your cover letter'
                    ], 403);
                } elseif (Application::where('user_id', $user->id)->where('job_id', $job->id)->exists()) {
                    return response()->json([
                        'message' => 'You have already applied for this job'
                    ], 403);
                } else {
                    $experiences = $user->experiences()->get();
                    $user_exp_years = 0;
                    foreach ($experiences as $experience) {
                        if ($job->employment == $experience->job_role) {
                            $start = new Carbon($experience->start_date);
                            $finish = $experience->finish_date ? new Carbon($experience->finish_date) : now();
                            Log::info('the exp_years befor calc :', [$user_exp_years]);
                            $experience_years = $start->diffInDays($finish) / 365.0;
                            $user_exp_years += $experience_years;
                            Log::info('the exp_years after calc :', [$user_exp_years]);
                        }
                    }
                    $user_exp_years = floor($user_exp_years);
                    Log::info('the exp_years after floor :', [$user_exp_years]);
                    $required_exp = $job->experience_years;
                    $baseMap = [
                        'College Degree' => 1,
                        'Diploma' => 2,
                        'Master Degree' => 3,
                        'PhD' => 4,
                    ];
                    Log::info('baseMap:', $baseMap);
                    $userEducations = $user->educations;
                    Log::info('User educations:', [$userEducations]);
                    foreach ($userEducations as $education) {
                        Log::info('baseMap value for ' . $education->academy_qualification . ':', [$baseMap[$education->academy_qualification] ?? 'Not found']);
                    }

                    $userExpLevels = $userEducations->map(function ($education) use ($baseMap) {
                        $userDegree = $education->academy_qualification;
                        $numericValue = $baseMap[$userDegree] ?? null;
                        if ($numericValue)
                            return [
                                'numeric_value' => $numericValue,
                            ];
                    });
                    Log::info('User experience levels:', [$userExpLevels]);
                    $maxValue = $userExpLevels->max('numeric_value');
                    Log::info('User\'s highest educational level:', [$maxValue]);
                    $educational_level = $job->educational_level;
                    $educational_level_value = $baseMap[$educational_level];
                    Log::info('Job\'s required educational level:', [$educational_level_value]);
                    Log::info('Comparison result:', [$maxValue < $educational_level_value]);
                    $languages = $user->languages()->get()->pluck('language')->toArray();
                    $required_languages = $job->languages;
                    $error_messages = [];
                    foreach ($required_languages as $required_language) {
                        if (!in_array($required_language, $languages)) {
                            $error_messages[] = "Missing language: $required_language";
                        }
                    }

                    if ($required_exp > $user_exp_years) {
                        $error_messages[] = "Experience mismatch";
                    }
                    if ($maxValue < $educational_level_value) {
                        $error_messages[] = "Insufficient educational level";
                    }
                    if (empty($error_messages)) {
                        $company_id = $job->company()->value('id');
                        $application = new Application();
                        $application->user_id = $user->id;
                        $application->job_id = $job->id;
                        $application->company_id = $company_id;
                        $application->application_date = now();
                        $application->save();
                        $company = $job->company()->first();
                        $company->notify(new NewApply(Auth::guard('user')->user(), $job));

                        return response()->json([
                            'message' => 'your application has been sent'
                        ], 200);
                    } else {
                        return response()->json([
                            'messages' => $error_messages
                        ], 403);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'cant apply to this job'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function list_applied_jobs()
    {
        /** @var App\Models\User $user */
        $user = Auth::guard('user')->user();
        if ($user) {
            $appliedJobs = $user->applications()
                ->with('job.company')
                ->get()
                ->map(function ($application) {
                    return [
                        'job' => $application->job,
                    ];
                });
            return response()->json([
                'data' => $appliedJobs
            ], 200);
        }
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }
    public function list_user_details(Request $request)
    {
        if (Auth::guard('user')->check() || Auth::guard('company')->check() || Auth::guard('admin')->check()) {
            $user = User::find($request->header('id'));
            if ($user) {
                return response()->json([
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id! user not found'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
