@extends('layouts.app', ['title' => 'All Requests', 'active' => 'all'])

@section('content')
    <div class="page-head">
        <h1>All Requests</h1>
    </div>

    <div class="total-pill">
        <x-icon name="total" /><span>Total Requests</span><strong>{{ $requests->count() }}</strong>
    </div>

    <section class="card">
        @include('partials.admin-table', ['showStatus' => true, 'emptyText' => 'No review requests have been submitted yet.'])
    </section>
@endsection
