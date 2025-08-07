<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlashcardSetsController;
use App\Http\Controllers\FlashcardsController;
use App\Http\Controllers\StudyCollectionsController;
use Illuminate\Support\Facades\Route;

//guest routes
Route::post("/signup", [AuthController::class, "signup"]);
Route::post("/login", [AuthController::class, "login"]);

//auth routes 
Route::get("/check-login", [AuthController::class, "checkLogin"])->middleware("auth:sanctum");

//protected routes
Route::middleware("auth:sanctum")->group(function () {

    //study collections
    Route::prefix("study-collections")->group(function () {
        Route::get("/", [StudyCollectionsController::class, "get"]);
        Route::get("/{id}", [StudyCollectionsController::class, "details"]);
        Route::post("/", [StudyCollectionsController::class, "store"]);
        Route::post("/{id}", [StudyCollectionsController::class, "update"]);
        Route::delete("/{id}", [StudyCollectionsController::class, "delete"]);
    });

    //flashcard sets
    Route::prefix("flashcard-sets")->group(function () {
        Route::get("/", [FlashcardSetsController::class, "get"]);
        Route::get("/{id}", [FlashcardSetsController::class, "details"]);
        Route::post("/", [FlashcardSetsController::class, "store"]);
        Route::patch("/{id}", [FlashcardSetsController::class, "update"]);
        Route::delete("/{id}", [FlashcardSetsController::class, "delete"]);
    });

    //flashcards
    Route::prefix("flashcards")->group(function () {
        Route::get("/", [FlashcardsController::class, "get"]);
        Route::get("/{id}", [FlashcardsController::class, "details"]);
        Route::post("/", [FlashcardsController::class, "store"]);
        Route::patch("/{id}", [FlashcardsController::class, "update"]);
        Route::delete("/{id}", [FlashcardsController::class, "delete"]);
    });
});
