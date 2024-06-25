<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\TargetJob;

class TargetJobController extends Controller
{
    public function listTargetJob()
    {
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $targetJob = $user->targetJob()->first();
            return response()->json([
                'data' => $targetJob
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function editTargetJob(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'Experience_years' => 'sometimes|required|numeric',
                'Education' => 'sometimes|required|max:255',
                'Joblevel' => 'sometimes|required|max:255',
                'job_type' => 'sometimes|required|array',
                'Job_roles' => 'sometimes|required|array',
                'work_cities' => 'sometimes|required|array',
                'current_job_status' => 'sometimes|required|max:255',
            ]);
            $targetJob = TargetJob::where('user_id', $user->id)->first();
            if ($targetJob) {
                $targetJob->update($validatedData);
                return response()->json([
                    'message' => 'edited successfully',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Target job not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
