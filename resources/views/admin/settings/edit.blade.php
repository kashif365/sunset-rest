@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <ul class="nav nav-tabs mb-3">
        @foreach(['business' => 'Business', 'ordering' => 'Ordering & Tax', 'seo' => 'SEO', 'announcement' => 'Announcement'] as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('admin.settings.edit', $key) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    <form action="{{ route('admin.settings.update', $tab) }}" method="post" enctype="multipart/form-data" style="max-width: 42rem;">
        @csrf @method('PUT')

        @if($tab === 'business')
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="business_name">Business name *</label>
                        <input class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $settings->get('business_name', 'Sunset Bagel Exchange')) }}" required maxlength="190">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" for="business_phone">Phone *</label>
                            <input class="form-control" id="business_phone" name="business_phone" value="{{ old('business_phone', $settings->get('business_phone', '(732) 361-8119')) }}" required maxlength="30">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="business_email">Email *</label>
                            <input class="form-control" id="business_email" name="business_email" type="email" value="{{ old('business_email', $settings->get('business_email', 'sunsetbagelexchange@gmail.com')) }}" required maxlength="190">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label" for="business_address">Address *</label>
                        <input class="form-control" id="business_address" name="business_address" value="{{ old('business_address', $settings->get('business_address', '3316 Sunset Ave., Ocean, NJ 07712')) }}" required maxlength="400">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="map_embed_url">Google Maps embed URL</label>
                        <input class="form-control" id="map_embed_url" name="map_embed_url" value="{{ old('map_embed_url', $settings->get('map_embed_url')) }}" maxlength="1000" placeholder="https://www.google.com/maps/embed?...">
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label" for="facebook_url">Facebook URL</label>
                            <input class="form-control" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings->get('facebook_url')) }}" maxlength="400">
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="instagram_url">Instagram URL</label>
                            <input class="form-control" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings->get('instagram_url')) }}" maxlength="400">
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="tiktok_url">TikTok URL</label>
                            <input class="form-control" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $settings->get('tiktok_url')) }}" maxlength="400">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label class="form-label" for="logo">Logo</label>
                            <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="favicon">Favicon</label>
                            <input class="form-control" type="file" id="favicon" name="favicon" accept="image/png,image/x-icon,image/svg+xml">
                        </div>
                    </div>
                </div>
            </div>
        @elseif($tab === 'ordering')
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 form-check form-switch">
                            <input type="hidden" name="ordering_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="ordering_enabled" name="ordering_enabled" value="1" @checked(old('ordering_enabled', $settings->bool('ordering_enabled', true)))>
                            <label class="form-check-label" for="ordering_enabled">Online ordering enabled</label>
                        </div>
                        <div class="col-6 form-check form-switch">
                            <input type="hidden" name="preordering_enabled" value="0">
                            <input class="form-check-input" type="checkbox" id="preordering_enabled" name="preordering_enabled" value="1" @checked(old('preordering_enabled', $settings->bool('preordering_enabled', true)))>
                            <label class="form-check-label" for="preordering_enabled">Allow pre-ordering (outside open hours)</label>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="min_order_amount">Minimum order ($)</label>
                            <input class="form-control" id="min_order_amount" name="min_order_amount" type="number" step="0.01" min="0" value="{{ old('min_order_amount', $settings->float('min_order_amount', 0)) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="pickup_interval_minutes">Pickup slot interval (min)</label>
                            <input class="form-control" id="pickup_interval_minutes" name="pickup_interval_minutes" type="number" min="5" value="{{ old('pickup_interval_minutes', $settings->int('pickup_interval_minutes', 15)) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="pickup_lead_minutes">Kitchen lead time (min)</label>
                            <input class="form-control" id="pickup_lead_minutes" name="pickup_lead_minutes" type="number" min="0" value="{{ old('pickup_lead_minutes', $settings->int('pickup_lead_minutes', 20)) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="advance_order_days">Advance order limit (days)</label>
                            <input class="form-control" id="advance_order_days" name="advance_order_days" type="number" min="0" value="{{ old('advance_order_days', $settings->int('advance_order_days', 7)) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="tax_rate">Tax rate (%)</label>
                            <input class="form-control" id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" value="{{ old('tax_rate', $settings->float('tax_rate', 6.625)) }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label" for="currency">Currency</label>
                            <input class="form-control" id="currency" name="currency" value="{{ old('currency', $settings->get('currency', 'USD')) }}" maxlength="3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="order_notification_email">Order notification email</label>
                            <input class="form-control" id="order_notification_email" name="order_notification_email" type="email" value="{{ old('order_notification_email', $settings->get('order_notification_email')) }}" maxlength="190">
                        </div>
                    </div>
                </div>
            </div>
        @elseif($tab === 'seo')
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="seo_title">Default SEO title</label>
                        <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $settings->get('seo_title')) }}" maxlength="190">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="seo_description">Default meta description</label>
                        <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="500">{{ old('seo_description', $settings->get('seo_description')) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="seo_keywords">Keywords</label>
                        <input class="form-control" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $settings->get('seo_keywords')) }}" maxlength="500">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="og_image">Default social share image</label>
                        <input class="form-control" type="file" id="og_image" name="og_image" accept="image/*">
                    </div>
                </div>
            </div>
        @elseif($tab === 'announcement')
            <div class="card">
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="announcement_enabled" value="0">
                        <input class="form-check-input" type="checkbox" id="announcement_enabled" name="announcement_enabled" value="1" @checked(old('announcement_enabled', $settings->bool('announcement_enabled')))>
                        <label class="form-check-label" for="announcement_enabled">Show announcement bar</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="announcement_text">Announcement text</label>
                        <input class="form-control" id="announcement_text" name="announcement_text" value="{{ old('announcement_text', $settings->get('announcement_text')) }}" maxlength="300">
                    </div>
                    <div class="form-check form-switch">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings->bool('maintenance_mode')))>
                        <label class="form-check-label" for="maintenance_mode">Maintenance mode flag <span class="text-body-secondary small">(run <code>php artisan down</code> to actually take the site offline)</span></label>
                    </div>
                </div>
            </div>
        @endif

        <button class="btn btn-primary mt-3" type="submit">Save Settings</button>
    </form>
@endsection
