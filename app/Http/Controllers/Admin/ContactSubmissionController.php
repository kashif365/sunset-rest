<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.contact-submissions.index', [
            'submissions' => ContactSubmission::query()
                ->when($request->query('filter') === 'unread', fn ($q) => $q->where('is_read', false))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
        ]);
    }

    public function show(ContactSubmission $contactSubmission)
    {
        $contactSubmission->update(['is_read' => true]);

        return view('admin.contact-submissions.show', ['submission' => $contactSubmission]);
    }

    public function destroy(ContactSubmission $contactSubmission)
    {
        $contactSubmission->delete();

        return redirect()->route('admin.contact-submissions.index')->with('success', 'Message deleted.');
    }
}
