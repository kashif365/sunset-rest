@extends('layouts.admin')

@section('title', 'Message from '.$submission->name)

@section('content')
    <div class="card mx-auto" style="max-width: 40rem;">
        <div class="card-body">
            <p class="mb-1"><strong>From:</strong> {{ $submission->name }} &lt;{{ $submission->email }}&gt;</p>
            @if($submission->phone)<p class="mb-1"><strong>Phone:</strong> {{ $submission->phone }}</p>@endif
            @if($submission->subject)<p class="mb-1"><strong>Subject:</strong> {{ $submission->subject }}</p>@endif
            <p class="mb-3"><strong>Received:</strong> {{ $submission->created_at->format('M j, Y g:i A') }}</p>
            <div class="p-3 bg-light rounded" style="white-space: pre-line;">{{ $submission->message }}</div>

            <div class="d-flex gap-2 mt-3">
                <a class="btn btn-outline-primary" href="mailto:{{ $submission->email }}"><i class="bi bi-reply-fill me-1"></i>Reply by email</a>
                <form action="{{ route('admin.contact-submissions.destroy', $submission) }}" method="post" data-confirm="Delete this message?">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">Delete</button>
                </form>
                <a class="btn btn-outline-secondary ms-auto" href="{{ route('admin.contact-submissions.index') }}">Back</a>
            </div>
        </div>
    </div>
@endsection
