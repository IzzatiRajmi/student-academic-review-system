@if ($requests->isEmpty())
    <div class="empty-state">
        <x-icon name="inbox" class="icon empty-icon" />
        <p>You have not submitted any review requests yet.</p>
        <a class="btn btn-primary" href="{{ route('student.requests.create') }}">Add Request</a>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr><th scope="col">No</th><th scope="col">Subject Code</th><th scope="col">Status</th><th scope="col">Date</th><th scope="col">Action</th></tr>
            </thead>
            <tbody>
            @foreach ($requests as $r)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Subject Code"><strong>{{ $r->subject_code }}</strong></td>
                    <td data-label="Status">@include('partials.status-badge', ['status' => $r->status])</td>
                    <td data-label="Date">{{ $r->created_at->format('d/m/Y') }}</td>
                    <td data-label="Action">
                        <div class="actions">
                            <button type="button" class="btn btn-sm btn-view" data-view='@json($r->viewData(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'
                                    aria-label="View request {{ $r->subject_code }}">View</button>
                            @if ($r->isPending())
                                <a class="btn btn-sm btn-outline" href="{{ route('student.requests.edit', $r->id) }}"
                                   aria-label="Edit request {{ $r->subject_code }}">Edit</a>
                                <button type="button" class="btn btn-sm btn-danger-soft"
                                        data-delete-id="{{ $r->id }}" data-delete-subject="{{ $r->subject_code }}"
                                        data-delete-url="{{ route('student.requests.destroy', $r->id) }}"
                                        aria-label="Delete request {{ $r->subject_code }}">Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
