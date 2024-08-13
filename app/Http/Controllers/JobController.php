<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Company;
use App\Notifications\ApproveRequest;
use App\Notifications\JobApproved;
use App\Notifications\JobDeleted;
use App\Notifications\JobDisabled;

class JobController extends Controller
{
    public function addJob(Request $request)
    {
        $validatedData = $request->validate([
            'job_title' => 'required|max:255',
            'employment' => 'required|max:255',
            'gender' => 'required|max:255',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'educational_level' => 'required|max:255',
            'career_level' => 'required|max:255',
            'languages' => 'required|array',
            'number_of_vacancies' => 'required|integer|min:1',
            'type_of_employment' => 'required|array',
            'city' => 'required|max:255',
            'address' => 'required|max:255',
            'min_salary' => 'required|max:255',
            'max_salary' => 'required|max:255',
            'job_description' => 'required|string',
            'cover_letter_required' => 'required|boolean',
            'experience_years' => 'required|integer|min:1'

        ]);

        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        if ($company) {
            if ($company->status == 1) {
                $job = new Job;
                $job = $company->jobs()->create($request->all());
                $job->disabled_by_admin = 1;
                $job->company_id = $company->id;
                $job->save();
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new ApproveRequest($job));
                }
                return response()->json([
                    'message' => 'Your request has been sent.',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'your account has been disabled'
                ], 403);
            }
        } else {
            return response([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function enable(Request $request)
    {
        /** @var App\Models\Compnay $company */
        if ($company = Auth::guard('company')->user()) {
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && !$job->disabled_by_admin && !$job->status && $job->company_id == $company->id) {
                $job->status = 1;
                $job->save();
                return response()->json([
                    'message' => 'enabled successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant enable job'
                ], 403);
            }
        } elseif ($admin = Auth::guard('admin')->user()) {
            /** @var App\Models\Admin $admin */
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && !$job->status) {
                $job->status = 1;
                $job->disabled_by_admin = 0;
                $job->save();
                $admin->AdminActions()->create([
                    'action_type' => 'approval for a job',
                    'object_type' => 'Job',
                    'object_id' => $job->id,
                ]);
                $company = $job->company()->first();
                $company->notify(new JobApproved($job));
                return response()->json([
                    'message' => 'enabled successfuly',
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
        if ($company = Auth::guard('company')->user()) {
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && $job->company_id == $company->id && $job->status) {
                $job->status = 0;
                $job->save();
                return response()->json([
                    'message' => 'disabled succssefuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant disable job'
                ], 403);
            }
        } elseif ($admin = Auth::guard('admin')->user()) {
            /** @var App\Models\Admin $admin */
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && $job->status) {
                $job->status = 0;
                $job->disabled_by_admin = 1;
                $job->save();
                $company = $job->company()->first();
                $admin->AdminActions()->create([
                    'action_type' => 'disable a job',
                    'object_type' => 'Job',
                    'object_id' => $job->id,
                ]);
                $company->notify(new JobDisabled($job));
                return response()->json([
                    'message' => 'disabled succssefuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function listALLJobs()
    {
        if (Auth::guard('company')->check() || Auth::guard('user')->check()) {
            $jobs = Job::where('status', 1)->with('company')->get();

            return response()->json([
                'data' => $jobs
            ], 200);
        } elseif (Auth::guard('admin')->check()) {
            $jobs = Job::all(['job_title', 'employment', 'company_id']);
            $jobsWithCompany = [];
            foreach ($jobs as $job) {
                $company_id = $job->company_id;
                $company = Company::where('id', $company_id)->first();
                if ($company) {
                    $company_name = $company->company_Name;
                    $jobWithCompany = [
                        'job_title' => $job->job_title,
                        'employment' => $job->employment,
                        'company_name' => $company_name
                    ];
                    array_push($jobsWithCompany, $jobWithCompany);
                }
            }
            return response()->json([
                'data' => $jobsWithCompany,
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function delete(Request $request)
    {
        if ($company = Auth::guard('company')->user()) {
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && $job->company_id == $company->id) {
                $job->delete();
                return response()->json([
                    'message' => 'deleted successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id',

                ], 404);
            }
        } elseif ($admin = Auth::guard('admin')->user()) {
            $job = Job::where('id', $request->id)->first();
            if ($job) {
                $company = $job->company()->first();
                $job->delete();
                $admin->AdminActions()->create([
                    'action_type' => 'delete a job',
                    'object_type' => 'Job',
                    'object_id' => $job->id,
                ]);
                $company->notify(new JobDeleted($job));
                return response()->json([
                    'message' => 'deleted successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id',

                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function editJob(Request $request)
    {
        $validatedData = $request->validate([
            'job_title' => 'sometimes|required|max:255',
            'employment' => 'sometimes|required|max:255',
            'gender' => 'sometimes|required|max:255',
            'min_age' => 'sometimes|required|integer',
            'max_age' => 'sometimes|required|integer',
            'educational_level' => 'sometimes|required|max:255',
            'career_level' => 'sometimes|required|max:255',
            'languages' => 'sometimes|required|array',
            'number_of_vacancies' => 'sometimes|required|integer|min:1',
            'type_of_employment' => 'sometimes|required|array',
            'city' => 'sometimes|required|max:255',
            'address' => 'sometimes|required|max:255',
            'min_salary' => 'sometimes|required|numeric',
            'max_salary' => 'sometimes|required|numeric',
            'job_description' => 'sometimes|required|string',
            'cover_letter_required' => 'sometimes|required|boolean',
            'experience_years' => 'sometimes|integer|min:1'
        ]);
        /** @var App/Models/Company $company */
        if ($company = Auth::guard('company')->user()) {
            $job = Job::where('id', $request->header('id'))->first();
            if ($job && $job->company_id = $company->id) {
                $job->update($validatedData);
                return response()->json([
                    'message' => 'edited successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant edit job',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }

    public function listJobDetails(Request $request)
    {
        if (Auth::guard('admin')->user() || Auth::guard('company')->user() || Auth::guard('user')->user()) {
            $job = Job::where('id', $request->header('id'))->first();
            if ($job) {
                return response()->json([
                    'data' => $job,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function list_my_jobs()
    {
        /** @var App\Models\User $user */
        $user = Auth::guard('user')->user();

        if ($user) {
            $targetJob = $user->targetJob()->first();
            if ($targetJob) {
                $jobRoles = $targetJob->Job_roles;

                $jobDetails = Job::whereIn('employment', $jobRoles)
                    ->where('status', 1)
                    ->with('company')
                    ->get()
                    ->map(function ($job) {
                        return [
                            'job' => $job,
                        ];
                    });

                return response()->json([
                    'data' => $jobDetails,
                ], 200);
            }
        }

        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    public function search_for_job(Request $request)
    {
        if (Auth::guard('user')->check() || Auth::guard('company')->check()) {
            $validatedData = $request->validate(['query' => 'required']);
            $query = $validatedData['query'];
            $results = Job::where('job_title', 'like', "%{$query}%")->with('company')->get();
            return response()->json([
                'suggestions' => $results
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function list_all_company_jobs(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $company = Company::find($request->id);
            if ($company) {
                $jobs = $company->jobs()->get();
                return response()->json([
                    'data' => $jobs
                ], 200);
            } else {
                return response()->json([
                    'message' => 'wrong id'
                ], 404);
            }
        } elseif (Auth::guard('user')->check() || Auth::guard('company')->check()) {
            $company = Company::find($request->id);
            if ($company) {
                $jobs = $company->jobs()->get();
                return response()->json([
                    'data' => $jobs
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
}
