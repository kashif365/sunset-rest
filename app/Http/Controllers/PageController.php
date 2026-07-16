<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\CateringPackage;
use App\Models\ContactSubmission;
use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\Page;
use App\Http\Requests\ContactRequest;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'page' => Page::active()->where('slug', 'about-us')->first(),
            'gallery' => GalleryImage::active()->take(6)->get(),
        ]);
    }

    public function catering()
    {
        return view('pages.catering', [
            'packages' => CateringPackage::active()->get(),
            'page' => Page::active()->where('slug', 'catering')->first(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'hours' => BusinessHour::orderBy('day_of_week')->get(),
        ]);
    }

    public function submitContact(ContactRequest $request, SettingsService $settings)
    {
        $submission = ContactSubmission::create($request->safe()->except('website'));

        $to = $settings->get('order_notification_email') ?: $settings->get('business_email');
        if ($to) {
            try {
                Mail::to($to)->send(new \App\Mail\ContactSubmissionMail($submission));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Thanks for reaching out! We will get back to you shortly.');
    }

    public function faq()
    {
        return view('pages.faq', ['faqs' => Faq::active()->get()]);
    }

    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('pages.show', compact('page'));
    }
}
