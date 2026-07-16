@extends('layouts.admin')

@section('title', $coupon->exists ? 'Edit Coupon: '.$coupon->code : 'New Coupon')

@section('content')
    <form action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="post" class="row g-3" style="max-width: 40rem;">
        @csrf
        @if($coupon->exists) @method('PUT') @endif

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="code">Code *</label>
                        <input class="form-control font-monospace text-uppercase" id="code" name="code" value="{{ old('code', $coupon->code) }}" required maxlength="40">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" for="type">Type *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed amount ($)</option>
                                <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percentage (%)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="value">Value *</label>
                            <input class="form-control" id="value" name="value" type="number" step="0.01" min="0.01" value="{{ old('value', $coupon->value) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="min_order">Minimum order ($)</label>
                            <input class="form-control" id="min_order" name="min_order" type="number" step="0.01" min="0" value="{{ old('min_order', $coupon->min_order) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="max_discount">Max discount ($)</label>
                            <input class="form-control" id="max_discount" name="max_discount" type="number" step="0.01" min="0" value="{{ old('max_discount', $coupon->max_discount) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="starts_at">Starts</label>
                            <input class="form-control" id="starts_at" name="starts_at" type="date" value="{{ old('starts_at', optional($coupon->starts_at)->toDateString()) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="expires_at">Expires</label>
                            <input class="form-control" id="expires_at" name="expires_at" type="date" value="{{ old('expires_at', optional($coupon->expires_at)->toDateString()) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="usage_limit">Usage limit</label>
                            <input class="form-control" id="usage_limit" name="usage_limit" type="number" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $coupon->exists ? $coupon->is_active : true))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">{{ $coupon->exists ? 'Save Changes' : 'Create Coupon' }}</button>
            <a class="btn btn-outline-secondary mt-3" href="{{ route('admin.coupons.index') }}">Cancel</a>
        </div>
    </form>
@endsection
