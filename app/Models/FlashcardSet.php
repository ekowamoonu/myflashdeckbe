<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardSet extends Model
{
    protected $guarded = [];

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class, "flashcard_set_id");
    }
}
