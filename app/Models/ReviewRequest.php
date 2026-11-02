<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRequest extends Model
{
    public const PENDING = 'Pending';
    public const APPROVED = 'Approved';
    public const REJECTED = 'Rejected';

    protected $fillable = ['review_category_id', 'subject_code', 'justification'];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    /** Student who submitted the request. */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ReviewCategory::class, 'review_category_id');
    }

    /** Admin who processed the request. */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::PENDING);
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    /** Status counts for the dashboard cards. */
    public static function stats(?int $userId = null): array
    {
        $counts = static::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $stats = [
            'pending' => (int) ($counts[self::PENDING] ?? 0),
            'approved' => (int) ($counts[self::APPROVED] ?? 0),
            'rejected' => (int) ($counts[self::REJECTED] ?? 0),
        ];

        return ['total' => array_sum($stats)] + $stats;
    }

    /** Data for the "View Academic Review Request" pop-up. */
    public function viewData(): array
    {
        return [
            'student_name' => $this->student->name,
            'subject_code' => $this->subject_code,
            'category' => $this->category?->name,
            'justification' => $this->justification,
            'submitted' => $this->created_at?->format('d/m/Y'),
            'status' => strtolower($this->status),
            'decided' => $this->processed_at?->format('d/m/Y'),
            'comment' => $this->admin_remarks ?? '',
        ];
    }
}
