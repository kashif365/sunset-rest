@extends('layouts.public')

@section('seo_title')FAQ — Sunset Bagel Exchange@endsection
@section('meta_description')Frequently asked questions about ordering, pickup, catering and allergens at Sunset Bagel Exchange in Ocean, NJ.@endsection

@push('structured-data')
    <script type="application/ld+json">@json(app(\App\Support\StructuredData::class)->faqPage(), JSON_UNESCAPED_SLASHES)</script>
@endpush

@section('content')
    <section class="section-burgundy py-5">
        <div class="container text-center">
            <p class="eyebrow text-yellow mb-1">Good to know</p>
            <h1 class="display-heading display-5 text-white">Frequently Asked Questions</h1>
        </div>
    </section>
    @include('partials.curve', ['fill' => '#69001F', 'flip' => true])

    <div class="container py-5" style="max-width: 52rem;">
        @if($faqs->isEmpty())
            <p class="text-center text-body-secondary">No FAQs yet — ask us anything on the <a href="{{ route('contact') }}">contact page</a>.</p>
        @else
            <div class="accordion" id="faqAccordion">
                @foreach($faqs as $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="faq-{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">{!! $faq->answer !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
