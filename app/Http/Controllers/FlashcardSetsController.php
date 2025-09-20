<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlashcardSetResource;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\StudyCollection;
use App\Services\OpenaiService;
use App\Services\TextExtractorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    public function store(Request $request, TextExtractorService $textExtractorService, OpenaiService $openaiService)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "uploadedFile" => "required|file|extensions:pdf|mimes:pdf",

        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        $defaultStudyCollection = StudyCollection::select("id")->where("user_id", Auth::user()->id)->first();

        // Extract text
        $extractedText = $textExtractorService->extractText($request->file("uploadedFile"));
        // Log::info("Extracted text: ", ["extractedText" => $extractedText]);

        $flashcardsContent = "";

        // Geenerate flashcards from text
        if ($extractedText != "") {
            $flashcardsContent = $openaiService->getFlashcardsContentFromOpenai($extractedText);
        }

        $newFlashcardSet = FlashcardSet::create([
            "user_id" => Auth::user()->id,
            "study_collection_id" => $defaultStudyCollection->id,
            "name" => $request->name,
        ]);

        $order = 1;
        foreach ($flashcardsContent as $flashcard) {
            Flashcard::create([
                "user_id" => Auth::user()->id,
                "flashcard_set_id" => $newFlashcardSet->id,
                "term" => $flashcard["term"],
                "definition" => $flashcard["definition"],
                "order" => $order,
            ]);
            $order++;
        }

        return response()->json([
            "data" => new FlashcardSetResource($newFlashcardSet)
        ], 201);
    }

    public function details($id)
    {
        $flashcardSet = FlashcardSet::with(["flashcards" => function ($query) {
            $query->select("id", "flashcard_set_id", "term", "definition", "order")->orderBy("order", "asc");
        }])->where("id", $id)->where("user_id", Auth::user()->id)->first();

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
