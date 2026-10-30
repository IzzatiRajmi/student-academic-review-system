{{-- Add Academic Review Request / Update Academic Review Request --}}
@php $isEdit = (bool) $reviewRequest; @endphp
@extends('layouts.app', ['title' => $isEdit ? 'Update Request' : 'Add Request', 'active' => 'add'])

@section('content')
    <div class="page-head">
        <h1>{{ $isEdit ? 'Update Academic Review Request' : 'Add Academic Review Request' }}</h1>
    </div>

    <section class="card form-card">
        @if ($errors->any())
            <div class="alert alert-error" role="alert"><x-icon name="x" /><span>Please fix the highlighted fields.</span></div>
        @endif

        <form method="POST" novalidate data-loading-form
              action="{{ $isEdit ? route('student.requests.update', $reviewRequest->id) : route('student.requests.store') }}">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="field @error('subject_code') has-error @enderror">
                <label for="subject_code">Subject Code <span class="req" aria-hidden="true">*</span></label>
                <input id="subject_code" name="subject_code" type="text" maxlength="7" required autocomplete="off"
                       placeholder="e.g. DES3083" value="{{ old('subject_code', $reviewRequest?->subject_code) }}"
                       aria-describedby="subject_help @error('subject_code') subject_err @enderror"
                       @error('subject_code') aria-invalid="true" @enderror data-uppercase>
                <small id="subject_help" class="hint">3 letters followed by 4 digits.</small>
                @error('subject_code')<small id="subject_err" class="error-text">{{ $message }}</small>@enderror
            </div>

            <div class="field @error('justification') has-error @enderror">
                <label for="justification">Justification <span class="req" aria-hidden="true">*</span></label>
                <textarea id="justification" name="justification" rows="7" maxlength="1000" required
                          placeholder="Explain why you are requesting an academic review"
                          aria-describedby="just_count @error('justification') just_err @enderror"
                          @error('justification') aria-invalid="true" @enderror
                          data-counter="just_count">{{ old('justification', $reviewRequest?->justification) }}</textarea>
                <small id="just_count" class="hint counter">0 / 1000</small>
                @error('justification')<small id="just_err" class="error-text">{{ $message }}</small>@enderror
            </div>

            <div class="form-actions">
                <a class="btn btn-outline" href="{{ route('student.requests.index') }}">Cancel</a>
                <button type="submit" class="btn btn-primary" data-loading-text="{{ $isEdit ? 'Updating…' : 'Submitting…' }}">
                    {{ $isEdit ? 'Update' : 'Submit' }}
                </button>
            </div>
        </form>
    </section>
@endsection
