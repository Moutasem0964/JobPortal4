<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use App\Models\Language;

class LanguageController extends Controller
{
    public function listLanguages()
    {
        /** @var App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            $languages = $user->languages()->get();
            return response()->json([
                'data' => $languages
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
    public function addLanguages(Request $request)
    {
        $validatedData = $request->validate([
            'languages.*.language' => 'required|string',
            'languages.*.proficiency' => 'required|string'
        ]);
        /** @var \App\Models\User $user */
        if ($user = Auth::guard('user')->user()) {
            foreach ($request->languages as $language) {
                $user->languages()->create($language);
            }
            return response()->json([
                'message' => 'added successfuly',
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function editLanguages(Request $request)
    {
        $validatedData = $request->validate([
            'languages.*.id' => 'required|exists:languages,id',
            'languages.*.language' => 'required|string',
            'languages.*.proficiency' => 'required|string'
        ]);

        if ($user = Auth::guard('user')->user()) {
            foreach ($request->languages as $languageData) {
                $language = Language::find($languageData['id']);
                if ($language && $language->user_id == $user->id) {
                    $language->update([
                        'language' => $languageData['language'],
                        'proficiency' => $languageData['proficiency']
                    ]);
                    return response()->json([
                        'message' => 'cant edit language',
                    ], 404);
                }
            }
            return response()->json([
                'message' => 'edited successfuly',
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
    public function deleteLanguages(Request $request)
    {
        $validatedData = $request->validate([
            'languages.*.id' => 'required|exists:languages,id',
        ]);

        if ($user = Auth::guard('user')->user()) {
            foreach ($request->languages as $languageData) {
                $language = Language::find($languageData['id']);
                if ($language && $language->user_id == $user->id) {
                    $language->delete();
                }
            }
            return response()->json([
                'message' => 'deleted successfuly',
            ], 200);
        } else {
            return response()->json([
                'message' => 'unauthenticated'
            ], 401);
        }
    }
}
