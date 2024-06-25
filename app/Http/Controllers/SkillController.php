<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\Skill;

class SkillController extends Controller
{
    public function listSkills()
    {
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $skills = $user->skills()->get();
            return response()->json([
                'data' => $skills
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function addSkill(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'skill' => 'required|string|max:255'
            ]);

            /** @var App\Models\User $user */
            $user->skills()->create($validatedData);
            return response()->json([
                'message' => 'Added successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function editSkill(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $validatedData = $request->validate([
                'skill' => 'required|max:255',
                'id' => 'required|exists:skills,id'
            ]);
            $skill = Skill::find($validatedData['id']);
            if ($skill && $skill->user_id == $user->id) {
                $skill->update(['skill' => $validatedData['skill']]);
                return response()->json([
                    'message' => 'updated successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant be edited'
                ], 403);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function deleteSkill(Request $request)
    {
        if ($user = Auth::guard('user')->user()) {
            $skill = Skill::find($request->header('id'));
            if ($skill && $skill->user_id == $user->id) {
                $skill->delete();
                return response()->json([
                    'message' => 'deleted successfuly',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'cant be deleted'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
