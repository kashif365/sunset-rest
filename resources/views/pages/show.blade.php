@extends('layouts.public')

@section('seo_title'){{ $page->seo_title ?: $page->title.' — Sunset Bagel Exchange' }}@endsection
@section('meta_description'){{ $page->meta_description ?: '' }}@endsection

@section('content')
    <section class="section-burgundy py-4">
        <div class="container">
            <h1 class="display-heading h1 text-white mb-0">{{ $page->title }}</h1>
        </div>
    </section>

    <div class="container py-5" style="max-width: 52rem;">
        <div class="page-content">{!! $page->content !!}</div>
    </div>
@endsection
