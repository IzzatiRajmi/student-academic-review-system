<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewCategory extends Model
{
    protected $fillable = ['name'];

    /** A category classifies many review requests. */
    public function reviewRequests(): HasMany
    {
        return $this->hasMany(ReviewRequest::class);
    }
}
