@php $showStatus = $showStatus ?? false; @endphp
@if ($requests->isEmpty())
    <div class="empty-state">
        <x-icon name="inbox" class="icon empty-icon" />
        <p>{{ $emptyText ?? 'No requests found.' }}</p>
    </div>
@else
    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">No</th><th scope="col">Student Name</th><th scope="col">Subject Code</th>
                @if ($showStatus)<th scope="col">Status</th>@endif
                <th scope="col">Date</th><th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($requests as $r)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Student Name">{{ $r->student->name }}</td>
                    <td data-label="Subject Code"><strong>{{ $r->subject_code }}</strong></td>
                    @if ($showStatus)
                        <td data-label="Status">@include('partials.status-badge', ['status' => $r->status])</td>
                    @endif
                    <td data-label="Date">{{ $r->created_at->format('d/m/Y') }}</td>
                    <td data-label="Action">
                        <div class="actions">
                            <button type="button" class="btn btn-sm btn-view" data-view='@json($r->viewData(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)'
                                    aria-label="View request from {{ $r->student->name }}">View</button>
                            @if ($r->isPending())
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.requests.process', $r) }}"
                                   aria-label="Process request from {{ $r->student->name }}">Process</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
