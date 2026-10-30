@extends('layouts.app', ['title' => 'Dashboard', 'active' => 'dashboard'])

@section('content')
    <div class="page-head">
        <div>
            <h1>Welcome, {{ auth()->user()->firstName() }}</h1>
            <p class="muted">Submit and manage your academic review requests.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('student.requests.create') }}">+ Add Request</a>
    </div>

    @include('partials.stat-cards')

    <section class="card">
        <div class="card-head">
            <h2>Recent Requests</h2>
            @if ($stats['total'] > $requests->count())
                <a class="link" href="{{ route('student.requests.index') }}">View all</a>
            @endif
        </div>
        @include('partials.student-table')
    </section>
@endsection
