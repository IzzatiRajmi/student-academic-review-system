{{-- Confirm Logout --}}
<div class="modal" id="logout-modal" role="dialog" aria-modal="true" aria-labelledby="logout-title" hidden>
    <div class="modal-box modal-sm">
        <div class="modal-icon"><x-icon name="logout" /></div>
        <h2 id="logout-title">Confirm Logout</h2>
        <p>Are you sure you want to logout?</p>
        <form method="POST" action="{{ route('logout') }}" class="modal-actions">
            @csrf
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
    </div>
</div>

@unless (auth()->user()->isAdmin())
{{-- Delete confirmation (students) --}}
<div class="modal" id="delete-modal" role="dialog" aria-modal="true" aria-labelledby="delete-title" hidden>
    <div class="modal-box modal-sm">
        <div class="modal-icon danger"><x-icon name="x" /></div>
        <h2 id="delete-title">Delete Request</h2>
        <p>Delete the review request for <strong data-delete-label></strong>? This cannot be undone.</p>
        <form method="POST" action="" class="modal-actions">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" value="" data-delete-target>
            <input type="hidden" name="back" value="{{ ($active ?? '') === 'dashboard' ? 'dashboard' : '' }}">
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
@endunless

{{-- View Academic Review Request --}}
<div class="modal" id="view-modal" role="dialog" aria-modal="true" aria-labelledby="view-title" hidden>
    <div class="modal-box">
        <h2 id="view-title">View Academic Review Request</h2>
        <div class="alert alert-info alert-inline">
            <x-icon name="info" /><span>You can only update or delete requests while the status is pending.</span>
        </div>
        <dl class="details">
            <dt>Student Name</dt><dd data-v="student_name"></dd>
            <dt>Subject Code</dt><dd data-v="subject_code"></dd>
            <dt>Justification</dt><dd data-v="justification" class="pre"></dd>
            <dt>Submitted Date</dt><dd data-v="submitted"></dd>
            <dt>Status</dt><dd data-v="status"></dd>
            <dt>Decision Date</dt><dd data-v="decided"></dd>
            <dt>Decision Comment</dt><dd data-v="comment" class="pre"></dd>
        </dl>
        <div class="modal-actions">
            <button type="button" class="btn btn-primary" data-close-modal>Close</button>
        </div>
    </div>
</div>
