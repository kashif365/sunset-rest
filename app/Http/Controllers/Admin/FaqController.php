<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use App\Support\HtmlSanitizer;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort_order')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.faqs.form', ['faq' => new Faq]);
    }

    public function store(FaqRequest $request)
    {
        Faq::create($this->payload($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.form', compact('faq'));
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($this->payload($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }

    private function payload(FaqRequest $request): array
    {
        $data = $request->validated();
        $data['answer'] = HtmlSanitizer::clean($data['answer']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
