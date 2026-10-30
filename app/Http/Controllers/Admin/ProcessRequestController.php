<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcessRequestController extends Controller
{
    /** Pending Requests (oldest first, so the queue is handled in order). */
    public function pending()
    {
        return view('admin.pending', [
            'requests' => ReviewRequest::pending()->with('student')->oldest()->oldest('id')->get(),
        ]);
    }

    /** All Requests (newest first). */
    public function index()
    {
        return view('admin.requests', [
            'requests' => ReviewRequest::with('student')->latest()->latest('id')->get(),
        ]);
    }

    public function edit(ReviewRequest $reviewRequest)
    {
        if (! $reviewRequest->isPending()) {
            return redirect()->route('admin.requests.pending')
                ->with('error', "This request has already been {$reviewRequest->status}.");
        }

        return view('admin.process', ['reviewRequest' => $reviewRequest->load('student')]);
    }

    public function update(Request $request, ReviewRequest $reviewRequest)
    {
        if (! $reviewRequest->isPending()) {
            return redirect()->route('admin.requests.pending')
                ->with('error', "This request has already been {$reviewRequest->status}.");
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in([ReviewRequest::APPROVED, ReviewRequest::REJECTED])],
            'comment' => ['nullable', 'string', 'max:500', 'required_if:decision,'.ReviewRequest::REJECTED],
        ], [
            'decision.required' => 'Please choose Approve or Reject.',
            'decision.in' => 'Please choose Approve or Reject.',
            'comment.required_if' => 'Please give a reason when rejecting a request.',
            'comment.max' => 'Comment must be 500 characters or fewer.',
        ]);

        // Only update if still pending (prevents two admins processing the same request)
        $updated = ReviewRequest::whereKey($reviewRequest->id)->pending()->update([
            'status' => $data['decision'],
            'admin_comment' => $data['comment'] ?: null,
            'decided_by' => $request->user()->id,
            'decided_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $updated) {
            return redirect()->route('admin.requests.pending')
                ->with('error', 'This request was already processed by someone else.');
        }

        return redirect()->route('admin.requests.pending')->with('success',
            "Request from {$reviewRequest->student->name} ({$reviewRequest->subject_code}) {$data['decision']}.");
    }
}
