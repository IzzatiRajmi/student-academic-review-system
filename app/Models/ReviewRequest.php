<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRequest extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    protected $fillable = ['subject_code', 'justification'];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
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
    public static function stats(?int $studentId = null): array
    {
        $counts = static::query()
            ->when($studentId, fn ($q) => $q->where('student_id', $studentId))
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
            'justification' => $this->justification,
            'submitted' => $this->created_at?->format('d/m/Y'),
            'status' => $this->status,
            'decided' => $this->decided_at?->format('d/m/Y'),
            'comment' => $this->admin_comment ?? '',
        ];
    }
}
