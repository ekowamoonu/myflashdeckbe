<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudyCollectionResource;
use App\Models\StudyCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class StudyCollectionsController extends Controller
{
    public function get()
    {
        $studyCollections = StudyCollection::where("user_id", Auth::user()->id)->orderBy("name", "desc")->get();

        return response()->json([
            "data" => StudyCollectionResource::collection($studyCollections)
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        $request["user_id"] = Auth::user()->id;

        $newVehicle = StudyCollection::create([
            "user_id" => $request->user_id,
            "name" => $request->name
        ]);

        return response()->json([
            "data" => new StudyCollectionResource($newVehicle)
        ], 200);
    }

    public function details($id)
    {
        $studyCollection = StudyCollection::where("id", $id)->where("user_id", Auth::user()->id)->first();

        return response()->json([
            "data" => new StudyCollectionResource($studyCollection)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }

        try {
            $studyCollectionToUpdate = StudyCollection::where("id", $id)->where("user_id", Auth::user()->id)->firstOrFail();
            $studyCollectionToUpdate->update([
                "name" => $request->name
            ]);
            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Study Collection not found"
            ], 404);
        }
    }

    public function delete(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 400);
        }


        try {
            $studyCollectionToUpdate = StudyCollection::where("id", $id)->where("user_id", Auth::user()->id)->firstOrFail();
            $studyCollectionToUpdate->delete();
            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Study Collection not found"
            ], 404);
        }
    }
}
