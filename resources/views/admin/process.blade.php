@extends('layouts.app', ['title' => 'Process Request', 'active' => 'pending'])

@section('content')
    <div class="page-head">
        <h1>Process Academic Review Request</h1>
    </div>

    <section class="card form-card">
        @if ($errors->any())
            <div class="alert alert-error" role="alert"><x-icon name="x" /><span>Please fix the highlighted fields.</span></div>
        @endif

        <dl class="details">
            <dt>Student Name</dt><dd>{{ $reviewRequest->student->name }}</dd>
            <dt>Subject Code</dt><dd>{{ $reviewRequest->subject_code }}</dd>
            <dt>Justification</dt><dd class="pre">{{ $reviewRequest->justification }}</dd>
            <dt>Submitted Date</dt><dd>{{ $reviewRequest->created_at->format('d/m/Y') }}</dd>
            <dt>Status</dt><dd>@include('partials.status-badge', ['status' => $reviewRequest->status])</dd>
        </dl>

        <form method="POST" action="{{ route('admin.requests.decide', $reviewRequest) }}" novalidate data-loading-form>
            @csrf
            <fieldset class="field @error('decision') has-error @enderror">
                <legend>Decision <span class="req" aria-hidden="true">*</span></legend>
                <label class="radio"><input type="radio" name="decision" value="approved" @checked(old('decision') === 'approved')> Approve</label>
                <label class="radio"><input type="radio" name="decision" value="rejected" @checked(old('decision') === 'rejected')> Reject</label>
                @error('decision')<small class="error-text">{{ $message }}</small>@enderror
            </fieldset>

            <div class="field @error('comment') has-error @enderror">
                <label for="comment">Add Comment <span class="muted">(optional, required when rejecting)</span></label>
                <textarea id="comment" name="comment" rows="4" maxlength="500" placeholder="Write a comment for the student"
                          @error('comment') aria-invalid="true" @enderror>{{ old('comment') }}</textarea>
                @error('comment')<small class="error-text">{{ $message }}</small>@enderror
            </div>

            <div class="form-actions">
                <a class="btn btn-outline" href="{{ route('admin.requests.pending') }}">Cancel</a>
                <button type="submit" class="btn btn-primary" data-loading-text="Submitting…">Submit</button>
            </div>
        </form>
    </section>
@endsection
