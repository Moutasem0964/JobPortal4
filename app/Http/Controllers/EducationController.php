<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\Education;

class EducationController extends Controller
{
    public function listEducations()
    {
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $educations = $user->educations()->get();
            return response()->json([
                'data' => $educations
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function addEducation(Request $request)
    {
        $validatedData = $request->validate([
            'academy_qualification' => 'required|string|max:255',
            'study_field' => 'required|string|max:255',
            'university_institution' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:finish_date',
            'finish_date' => 'required|date|after_or_equal:start_date',
            'grade' => 'required|numeric|min:0',
        ]);
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $user->educations()->create($validatedData);
            return response()->json([
                'message' => 'added successfuly',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
    public function editEducation(Request $request)
    {
        $validatedData = $request->validate([
            'academy_qualification' => 'sometimes|required|string|max:255',
            'study_field' => 'sometimes|required|string|max:255',
            'university_institution' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date|before_or_equal:finish_date',
            'finish_date' => 'sometimes|required|date|after_or_equal:start_date',
            'grade' => 'sometimes|required|numeric|min:0',
        ]);
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $user->educations()->update($validatedData);
            return response()->json([
                'message' => 'Updated successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'Unauthenticated',
        ], 401);
    }
    public function deleteEducation(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $education = Education::find($request->header('id'));
            if ($education && $education->user_id == $user->id) {
                $education->delete();
                return response()->json([
                    'message' => 'deleted successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant find education',

                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'

            ], 401);
        }
    }
}
