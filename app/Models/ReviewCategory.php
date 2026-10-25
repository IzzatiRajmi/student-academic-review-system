<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewCategory extends Model
{
    protected $fillable = ['name'];

public function reviewRequests()
{
    return $this->hasMany(ReviewRequest::class);
}
}
