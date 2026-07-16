<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"></head>
<body style="margin:0; padding:16px; font-family: Arial, Helvetica, sans-serif; color:#201713;">
    <h1 style="font-size:18px; color:#69001F;">New website message</h1>
    <p style="font-size:14px;">
        <strong>From:</strong> {{ $submission->name }} &lt;{{ $submission->email }}&gt;<br>
        @if($submission->phone)<strong>Phone:</strong> {{ $submission->phone }}<br>@endif
        @if($submission->subject)<strong>Subject:</strong> {{ $submission->subject }}<br>@endif
        <strong>Received:</strong> {{ $submission->created_at->format('M j, Y g:i A') }}
    </p>
    <div style="padding:12px; background:#FFF5D8; border-radius:8px; font-size:14px; white-space:pre-line;">{{ $submission->message }}</div>
    <p style="font-size:13px; margin-top:16px;"><a href="{{ route('admin.contact-submissions.show', $submission) }}" style="color:#B51F2A;">View in admin panel →</a></p>
</body>
</html>
