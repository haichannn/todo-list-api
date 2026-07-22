<?php

namespace App\Models;

use App\Http\Resources\TodoCollection;
use App\Policies\TodoPolicy;
use Database\Factories\TodoFactory;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description'])]
#[UseFactory(TodoFactory::class)]
#[UsePolicy(TodoPolicy::class)]
#[CollectedBy(TodoCollection::class)]
class Todo extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
