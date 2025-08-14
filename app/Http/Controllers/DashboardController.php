<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlashcardSetResource;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\StudyCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function get()
    {
        $numberOfCollections = StudyCollection::where("user_id", Auth::user()->id)->count();
        $numberOfFlashcardSets = FlashcardSet::where("user_id", Auth::user()->id)->count();
        $numberOfFlashcards = Flashcard::where("user_id", Auth::user()->id)->count();
        $recentlyCreatedFlashcardSets = FlashcardSet::where("user_id", Auth::user()->id)->orderBy("created_at", "desc")->limit(4)->get();


        return response()->json([
            "data" => [
                "numberOfCollections" => $numberOfCollections,
                "numberOfFlashcardSets" => $numberOfFlashcardSets,
                "numberOfFlashcards" => $numberOfFlashcards,
                "recentlyCreatedFlashcardSets" => FlashcardSetResource::collection($recentlyCreatedFlashcardSets),
                "userName" => Auth::user()->name
            ]
        ]);
    }
}
