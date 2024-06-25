<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;


class AdminController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        $admin = Admin::where('email', $request->input('email'))->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'You are logged in.',
            'token' => $token,
        ],200);
    }
    public function listAllUsers()
    {
        if (Auth::guard('admin')->user()) {
            $users = User::all();
            return response()->json([
                'all users', $users,
            ],200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ],401);
        }
    }
    public function list_all_approve_requests()
    {
        if (Auth::guard('admin')->user()) {
            $jobs = Job::where('status', 0)->get();
            return response()->json([
                'all jobs' => $jobs,
            ],200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ],401);
        }
    }
    public function search(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            if ($request->input('type') == 'user') {
                $user = User::where('email', $request->input('item'))->first();
                if ($user) {
                    return response()->json([
                        'data' => $user,
                    ],200);
                } else {
                    return response()->json([
                        'message' => 'User not found'
                    ], 404);
                }
            } elseif ($request->input('type') == 'company') {
                $company = Company::where('company_Name', $request->input('item'))->first();
                if ($company) {
                    return response()->json([
                        'data' => $company,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Company not found'
                    ], 404);
                }
            } elseif ($request->input('type') == 'job') {
                $job = Job::where('job_title', $request->input('item'))->get();
                if ($job) {
                    return response()->json([
                        'data' => $job,
                    ],200);
                } else {
                    return response()->json([
                        'message' => 'Job not found'
                    ], 404);
                }
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
