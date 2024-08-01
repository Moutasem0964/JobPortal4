<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Notifications\AccountDisabled;
use App\Notifications\CompanyRegistered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Js;

class CompanyController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255|unique:companies',
            'password' => 'required|min:8',
            'company_Name' => 'required|max:255',
            'company_website' => 'required|url|max:255',
            'city' => 'required|max:255',
            'areas_of_work' => 'required|array',
            'RP_first_name' => 'required|max:255',
            'RP_last_name' => 'required|max:255',
            'RP_job_title' => 'required|max:255',
            'RP_phone_number' => 'required|numeric',
        ]);
        $company = new Company();
        $company->email = $request->input('email');
        $company->password = Hash::make($request->input('password'));
        $company->company_Name = $request->input('company_Name');
        $company->company_wesite = $request->input('company_website');
        $company->city = $request->input('city');
        $company->areas_of_work = $request->input('areas_of_work');
        $company->RP_first_name = $request->input('RP_first_name');
        $company->RP_last_name = $request->input('RP_last_name');
        $company->RP_job_title = $request->input('RP_job_title');
        $company->RP_phone_number = $request->input('RP_phone_number');
        $company->save();
        $token = $company->createToken('companyToken', ['company'])->plainTextToken;
        $admins=Admin::all();
        foreach($admins as $admin){
            $admin->notify(new CompanyRegistered($company));
        }
        return response()->json([
            'message' => 'welcome',
            'token' => $token,
        ], 200);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);
        $company = Company::where('email', $request->input('email'))->first();
        if ($company && Hash::check($request->input('password'), $company->password)) {
            $token = $company->createToken('companyToken', ['company'])->plainTextToken;
            return response()->json([
                'message' => 'welcome back',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'wrong credential'
            ], 401);
        }
    }
    public function listAllCompanies()
    {
        if (Auth::guard('company')->user() || Auth::guard('user')->user()) {
            $companies = Company::where('status', 1)->get();
            return response()->json([
                'all companies' => $companies,
            ], 200);
        } elseif (Auth::guard('admin')->user()) {
            $companies = Company::all();
            return response()->json([
                'all companies' => $companies,
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }

    public function delete(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            $company = Company::Where('id', $request->header('id'))->first();
            if ($company) {
                $company->delete();
                return response()->json([
                    'messgae' => 'deleted successfuly',
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
    public function disable(Request $request)
    {
        /** @var App\Models\Admin $admin */
        if ($admin=Auth::guard('admin')->user()) {
            $company = Company::where('id', $request->header('id'))->first();
            if ($company) {
                $company->status = 0;
                $company->save();
                $admin->AdminActions()->create([
                    'action_type' => 'disable a compnay account',
                    'object_type' => 'Company',
                    'object_id' => $company->id,
                ]);
                $company->notify(new AccountDisabled());
                return response()->json([
                    'message' => 'disabled successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant find comapny'
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
        if (Auth::guard('admin')->user()) {
            $company = Company::where('id', $request->id)->first();
            if ($company) {
                $company->status = 1;
                $company->save();
                return response()->json([
                    'message' => 'enabled successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant find comapny'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthentictaed'
            ], 401);
        }
    }
    public function updateProfile(Request $request)
    {
        $rules = [
            'email' => 'sometimes|required|email|max:255',
            'password' => 'sometimes|required|min:8',
            'company_Name' => 'sometimes|required|max:255',
            'company_website' => 'sometimes|required|url|max:255',
            'city' => 'sometimes|required|max:255',
            'areas_of_work' => 'sometimes|required|array',
            'RP_first_name' => 'sometimes|required|max:255',
            'RP_last_name' => 'sometimes|required|max:255',
            'RP_job_title' => 'sometimes|required|max:255',
            'RP_phone_number' => 'sometimes|required|numeric',
        ];



        $validatedData = $request->validate($rules);
        /** @var App\Models\Company */
        if ($company = Auth::guard('company')->user()) {
            // Get only the fields that were sent with the request
            $dataToUpdate = $request->only(array_keys($validatedData));

            // Hash password if it's present in the data to update
            if (isset($dataToUpdate['password'])) {
                $dataToUpdate['password'] = Hash::make($dataToUpdate['password']);
            }

            // Update the company with the data to update
            $company->fill($dataToUpdate);
            $company->save();

            return response()->json(['message' => 'Profile updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }
    public function list_compnay_details(Request $request)
    {
        if (Auth::guard('user')->check() || Auth::guard('company')->check() || Auth::guard('admin')->check()) {
            $company = Company::where('id', $request->header('id'))->first();
            return response()->json([
                'data' => $company,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
    public function list_applicants()
    {
        /** @var App\Models\Company */
        if ($company = Auth::guard('company')->user()) {
            $applications = $company->applications()->get();
            if ($applications->isNotEmpty()) {
                $data = [];
                foreach ($applications as $application) {
                    $job = $application->job;
                    $user = $application->user;
                    $application->user()->get();
                    $data[] = [
                        'job' => $job,
                        'user' => $user
                    ];
                }
                return response()->json([
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'message' => 'there is no applications'
            ], 404);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function search_for_Company(Request $request)
    {
        if (Auth::guard('user')->check() || Auth::guard('company')->check()) {
            $validated=$request->validate([
                'query'=>'required'
            ]);
            $query=$validated['query'];
            $results = Company::where('company_Name', 'like', "%{$query}%")->limit(10)->get();
            return response()->json([
                'suggestions' => $results
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
