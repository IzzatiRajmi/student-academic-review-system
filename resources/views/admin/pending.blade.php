@extends('layouts.app', ['title' => 'Pending Requests', 'active' => 'pending'])

@section('content')
    <div class="page-head">
        <h1>Pending Requests</h1>
    </div>

    <div class="total-pill">
        <x-icon name="pending" /><span>Pending Requests</span><strong>{{ $requests->count() }}</strong>
    </div>

    <section class="card">
        @include('partials.admin-table', ['emptyText' => 'All caught up — there are no pending requests.'])
    </section>
@endsection
