@extends('layouts.admin')

@section('title', $faq->exists ? 'Edit FAQ' : 'New FAQ')

@section('content')
    <form action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="post" class="row g-3" style="max-width: 50rem;">
        @csrf
        @if($faq->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="question">Question *</label>
                        <input class="form-control" id="question" name="question" value="{{ old('question', $faq->question) }}" required maxlength="500">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="answer">Answer *</label>
                        <textarea class="form-control" id="answer" name="answer" rows="5" required maxlength="4000">{{ old('answer', $faq->answer) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="sort_order">Sort order</label>
                            <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $faq->exists ? $faq->is_active : true))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $faq->exists ? 'Save Changes' : 'Create FAQ' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.faqs.index') }}">Cancel</a>
        </div>
    </form>
@endsection
