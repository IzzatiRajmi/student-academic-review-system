@extends('layouts.app', ['title' => 'Dashboard', 'active' => 'dashboard'])

@section('content')
    <div class="page-head">
        <div>
            <h1>Welcome, {{ auth()->user()->firstName() }}</h1>
            <p class="muted">Manage and process academic review requests.</p>
        </div>
    </div>

    @include('partials.stat-cards')

    <section class="card">
        <div class="card-head">
            <h2>Pending Requests</h2>
            @if ($stats['pending'] > $requests->count())
                <a class="link" href="{{ route('admin.requests.pending') }}">View all {{ $stats['pending'] }}</a>
            @endif
        </div>
        @include('partials.admin-table', ['emptyText' => 'All caught up — there are no pending requests.'])
    </section>
@endsection
