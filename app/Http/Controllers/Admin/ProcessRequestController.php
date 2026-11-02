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
            'requests' => ReviewRequest::pending()->with(['student', 'category'])->oldest()->oldest('id')->get(),
        ]);
    }

    /** All Requests (newest first). */
    public function index()
    {
        return view('admin.requests', [
            'requests' => ReviewRequest::with(['student', 'category'])->latest()->latest('id')->get(),
        ]);
    }

    public function edit(ReviewRequest $reviewRequest)
    {
        if (! $reviewRequest->isPending()) {
            return $this->alreadyProcessed($reviewRequest);
        }

        return view('admin.process', ['reviewRequest' => $reviewRequest->load(['student', 'category'])]);
    }

    public function update(Request $request, ReviewRequest $reviewRequest)
    {
        if (! $reviewRequest->isPending()) {
            return $this->alreadyProcessed($reviewRequest);
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in([ReviewRequest::APPROVED, ReviewRequest::REJECTED])],
            'admin_remarks' => ['nullable', 'string', 'max:500', 'required_if:decision,'.ReviewRequest::REJECTED],
        ], [
            'decision.required' => 'Please choose Approve or Reject.',
            'decision.in' => 'Please choose Approve or Reject.',
            'admin_remarks.required_if' => 'Please give a reason when rejecting a request.',
            'admin_remarks.max' => 'Remarks must be 500 characters or fewer.',
        ]);

        // Only update if still pending (prevents two admins processing the same request)
        $updated = ReviewRequest::whereKey($reviewRequest->id)->pending()->update([
            'status' => $data['decision'],
            'admin_remarks' => $data['admin_remarks'] ?: null,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $updated) {
            return redirect()->route('admin.requests.pending')
                ->with('error', 'This request was already processed by someone else.');
        }

        $status = strtolower($data['decision']);

        return redirect()->route('admin.requests.pending')->with('success',
            "Request from {$reviewRequest->student->name} ({$reviewRequest->subject_code}) {$status}.");
    }

    private function alreadyProcessed(ReviewRequest $reviewRequest)
    {
        $status = strtolower($reviewRequest->status);

        return redirect()->route('admin.requests.pending')
            ->with('error', "This request has already been {$status}.");
    }
}
