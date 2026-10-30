@extends('layouts.app', ['title' => 'My Requests', 'active' => 'my'])

@section('content')
    <div class="page-head">
        <h1>My Academic Review Request</h1>
        <a class="btn btn-primary" href="{{ route('student.requests.create') }}">+ Add Request</a>
    </div>

    <div class="alert alert-info alert-inline">
        <x-icon name="info" /><span>You can only update or delete requests while the status is pending.</span>
    </div>

    <section class="card">
        @include('partials.student-table')
    </section>
@endsection
