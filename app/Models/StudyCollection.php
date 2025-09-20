<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyCollection extends Model
{
    protected $guarded = [];

    public function flashcardSets()
    {
        return $this->hasMany(FlashcardSet::class,'study_collection_id');
    }
}
