<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\Experience;

class ExperienceController extends Controller
{
    public function listExperiences()
    {
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $experiences = $user->experiences()->get();
            return response()->json([
                'data' => $experiences
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function addExperience(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'company' => 'required|max:255',
                'job_title' => 'required|max:255',
                'job_role' => 'required|max:255',
                'start_date' => 'required|date',
                'finish_date' => 'nullable|date|after_or_equal:start_date',
                'description' => 'required|max:5000',
            ]);

            /** @var App\Models\User $user */
            $user->experiences()->create($validatedData);
            return response()->json([
                'message' => 'Added successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
    public function editExperience(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'company' => 'sometimes|required|max:255',
                'job_title' => 'sometimes|required|max:255',
                'job_role' => 'sometimes|required|max:255',
                'start_date' => 'sometimes|required|date',
                'finish_date' => 'sometimes|nullable|date|after_or_equal:start_date',
                'description' => 'sometimes|required|max:5000',
            ]);

            /** @var App\Models\User $user */
            $experience = Experience::find($request->header('id'));
            if ($experience && $experience->user_id == $user->id) {
                $experience->update($validatedData);
                return response()->json([
                    'message' => 'Updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Experience not found or not owned by the user',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
    public function deleteExperience(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {


            $experience = Experience::find($request->header('id'));
            if ($experience) {
                if ($experience->user_id == $user->id) {
                    $experience->delete();
                    return response()->json([
                        'message' => 'Deleted successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Experience record does not belong to the user',
                    ], 403);
                }
            } else {
                return response()->json([
                    'message' => 'Experience record not found',
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
