@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
            <a class="btn btn-outline-secondary {{ request('filter') !== 'unread' ? 'active' : '' }}" href="{{ route('admin.contact-submissions.index') }}">All</a>
            <a class="btn btn-outline-secondary {{ request('filter') === 'unread' ? 'active' : '' }}" href="{{ route('admin.contact-submissions.index', ['filter' => 'unread']) }}">Unread</a>
        </div>
    </div>

    <div class="card">
        <ul class="list-group list-group-flush">
            @forelse($submissions as $submission)
                <li class="list-group-item d-flex justify-content-between align-items-start gap-2 {{ $submission->is_read ? '' : 'bg-light' }}">
                    <div>
                        <a class="{{ $submission->is_read ? '' : 'fw-bold' }}" href="{{ route('admin.contact-submissions.show', $submission) }}">
                            {{ $submission->name }} — {{ $submission->subject ?: \Illuminate\Support\Str::limit($submission->message, 60) }}
                        </a>
                        <div class="small text-body-secondary">{{ $submission->email }} @if($submission->phone) • {{ $submission->phone }} @endif</div>
                    </div>
                    <span class="small text-body-secondary text-nowrap">{{ $submission->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="list-group-item text-body-secondary text-center py-4">No messages.</li>
            @endforelse
        </ul>
    </div>
    <div class="mt-3">{{ $submissions->links() }}</div>
@endsection
