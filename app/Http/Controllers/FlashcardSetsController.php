<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlashcardSetResource;
use App\Models\FlashcardSet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FlashcardSetsController extends Controller
{
    public function get()
    {
        $flashcardSets = FlashcardSet::where("user_id", Auth::user()->id)->orderBy("name", "asc")->get();

        return response()->json([
            "data" => FlashcardSetResource::collection($flashcardSets)
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "studyCollectionId" => "required",
            "name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        $newFlashcardSet = FlashcardSet::create([
            "user_id" => Auth::user()->id,
            "study_collection_id" => $request->studyCollectionId,
            "name" => $request->name,
        ]);

        return response()->json([
            "data" => new FlashcardSetResource($newFlashcardSet)
        ], 201);
    }

    public function details($id)
    {
        $flashcardSet = FlashcardSet::where("id", $id)->where("user_id", Auth::user()->id)->first();

        if (!$flashcardSet) {
            return response()->json([
                "message" => ["Flashcard set not found"]
            ], 400);
        }

        return response()->json([
            "data" => new FlashcardSetResource($flashcardSet)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "studyCollectionId" => "required",
            "name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        try {
            $flashcardSetToUpdate = FlashcardSet::where("id", $id)->where("user_id", Auth::user()->id)->firstOrFail();
            $flashcardSetToUpdate->update([
                "name" => $request->name
            ]);
            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Flashcard set not found"
            ], 404);
        }
    }

    public function delete(Request $request, $id)
    {
        FlashcardSet::where("id", $id)->where("user_id", Auth::user()->id)->delete();
        return response()->noContent();
    }
}
