<section class="stats" aria-label="Request summary">
    @foreach ([
        ['total', 'total', 'Total Requests'],
        ['pending', 'pending', 'Pending'],
        ['approved', 'check', 'Approved'],
        ['rejected', 'x', 'Rejected'],
    ] as [$key, $icon, $label])
        <div class="stat stat-{{ $key }}">
            <x-icon :name="$icon" class="icon stat-icon" />
            <span class="stat-label">{{ $label }}</span>
            <span class="stat-value">{{ $stats[$key] }}</span>
        </div>
    @endforeach
</section>
