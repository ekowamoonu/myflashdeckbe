<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashcardSetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "studyCollectionId" => $this->study_collection_id,
            "name" => $this->name,
            "createdAtRaw" => $this->created_at,
            "createdAtFormatted" => \Carbon\Carbon::parse($this->created_at)->format("d M, Y"),
            "updatedAtRaw" => $this->updated_at,
            "updatedAtFormatted" => \Carbon\Carbon::parse($this->updated_at)->format("d M, Y"),
        ];
    }
}
