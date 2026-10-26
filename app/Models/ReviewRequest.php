<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRequest extends Model
{
    protected $fillable = [
    'user_id', 'review_category_id', 'subject_code', 'justification',
    'status', 'processed_by', 'admin_remarks', 'processed_at',
];

protected $casts = ['processed_at' => 'datetime'];

public function user()      { return $this->belongsTo(User::class); }
public function category()  { return $this->belongsTo(ReviewCategory::class, 'review_category_id'); }
public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
}
