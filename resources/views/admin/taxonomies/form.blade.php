@extends('layouts.admin')

@section('title', ($record->exists ? 'Edit ' : 'New ').$title)

@section('content')
    <form action="{{ $record->exists ? route('admin.'.$type.'.update', $record) : route('admin.'.$type.'.store') }}" method="post" class="row g-3" style="max-width: 34rem;">
        @csrf
        @if($record->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">Name *</label>
                        <input class="form-control" id="name" name="name" data-slug-source="#slug" value="{{ old('name', $record->name) }}" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug *</label>
                        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $record->slug) }}" required maxlength="110">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="icon">Bootstrap Icon class <span class="text-body-secondary small">(optional, e.g. bi-egg-fried)</span></label>
                        <input class="form-control" id="icon" name="icon" value="{{ old('icon', $record->icon) }}" maxlength="80">
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $record->exists ? 'Save Changes' : 'Create' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.'.$type.'.index') }}">Cancel</a>
        </div>
    </form>
@endsection
