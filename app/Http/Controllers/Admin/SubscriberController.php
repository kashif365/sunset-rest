<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('admin.subscribers.index', [
            'subscribers' => Subscriber::latest()->paginate(50),
        ]);
    }

    public function toggle(Subscriber $subscriber)
    {
        $subscriber->update(['is_active' => ! $subscriber->is_active]);

        return back()->with('success', 'Subscriber updated.');
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'active', 'subscribed_at']);
            Subscriber::chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $subscriber) {
                    fputcsv($out, [
                        $subscriber->email,
                        $subscriber->is_active ? 'yes' : 'no',
                        $subscriber->created_at->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'subscribers.csv', ['Content-Type' => 'text/csv']);
    }
}
