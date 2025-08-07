<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlashcardResource;
use App\Models\Flashcard;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FlashcardsController extends Controller
{
    public function get()
    {
        $flashcards = Flashcard::where("user_id", Auth::user()->id)->orderBy("name", "asc")->get();

        return response()->json([
            "data" => FlashcardResource::collection($flashcards)
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "flashcardSetId" => "required",
            "name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        $newFlashcard = Flashcard::create([
            "user_id" => Auth::user()->id,
            "flashcard_set_id" => $request->flashcardSetId,
            "name" => $request->name,
            "term" => $request->term,
            "definition" => $request->definition,
            "order" => $request->order,
        ]);

        return response()->json([
            "data" => new FlashcardResource($newFlashcard)
        ], 201);
    }

    public function details($id)
    {
        $flashcard = Flashcard::where("id", $id)->where("user_id", Auth::user()->id)->first();

        if (!$flashcard) {
            return response()->json([
                "message" => ["Flashcard  not found"]
            ], 400);
        }

        return response()->json([
            "data" => new FlashcardResource($flashcard)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "flashcardSetId" => "required",
            "term" => "required",
            "definition" => "required",
            "order" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        try {
            $flashcardToUpdate = Flashcard::where("id", $id)->where("user_id", Auth::user()->id)->firstOrFail();
            $flashcardToUpdate->update([
                "name" => $request->name
            ]);
            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Flashcard not found"
            ], 404);
        }
    }

    public function delete($id)
    {
        Flashcard::where("id", $id)->where("user_id", Auth::user()->id)->delete();
        return response()->noContent();
    }
}
