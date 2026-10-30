<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequestForm;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;

/**
 * Student request management.
 * Rules: a student only sees their own requests, and can only
 * update/delete while the status is pending.
 */
class ReviewRequestController extends Controller
{
    public function index(Request $request)
    {
        return view('student.requests', [
            'requests' => $request->user()->reviewRequests()->with('student')->latest()->latest('id')->get(),
        ]);
    }

    public function create()
    {
        return view('student.form', ['reviewRequest' => null]);
    }

    public function store(ReviewRequestForm $form)
    {
        $data = $form->validated();
        $form->user()->reviewRequests()->create($data);

        return redirect()->route('student.requests.index')
            ->with('success', "Request for {$data['subject_code']} submitted successfully.");
    }

    public function edit(Request $request, int $id)
    {
        $reviewRequest = $this->ownRequest($request, $id);

        if (! $reviewRequest->isPending()) {
            return redirect()->route('student.requests.index')
                ->with('error', "This request has already been {$reviewRequest->status} and can no longer be updated.");
        }

        return view('student.form', compact('reviewRequest'));
    }

    public function update(ReviewRequestForm $form, int $id)
    {
        $reviewRequest = $this->ownRequest($form, $id);

        if (! $reviewRequest->isPending()) {
            return redirect()->route('student.requests.index')
                ->with('error', "This request has already been {$reviewRequest->status} and can no longer be updated.");
        }

        $data = $form->validated();
        $reviewRequest->update($data);

        return redirect()->route('student.requests.index')
            ->with('success', "Request for {$data['subject_code']} updated successfully.");
    }

    public function destroy(Request $request, int $id)
    {
        $reviewRequest = $request->user()->reviewRequests()->find($id);
        $back = $request->input('back') === 'dashboard' ? 'student.dashboard' : 'student.requests.index';

        if (! $reviewRequest) {
            return redirect()->route($back)->with('error', 'Request not found.');
        }
        if (! $reviewRequest->isPending()) {
            return redirect()->route($back)->with('error', 'Only pending requests can be deleted.');
        }

        $reviewRequest->delete();

        return redirect()->route($back)->with('success', "Request for {$reviewRequest->subject_code} deleted.");
    }

    /** Find a request owned by the logged-in student, otherwise 404. */
    private function ownRequest(Request $request, int $id): ReviewRequest
    {
        return $request->user()->reviewRequests()->findOrFail($id);
    }
}
