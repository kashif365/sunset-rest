<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly ImageService $images,
    ) {}

    public function edit(string $tab = 'business')
    {
        abort_unless(in_array($tab, ['business', 'ordering', 'seo', 'announcement'], true), 404);

        return view('admin.settings.edit', [
            'tab' => $tab,
            'settings' => $this->settings,
        ]);
    }

    public function update(Request $request, string $tab)
    {
        match ($tab) {
            'business' => $this->updateBusiness($request),
            'ordering' => $this->updateOrdering($request),
            'seo' => $this->updateSeo($request),
            'announcement' => $this->updateAnnouncement($request),
            default => abort(404),
        };

        return redirect()->route('admin.settings.edit', $tab)->with('success', 'Settings saved.');
    }

    private function updateBusiness(Request $request): void
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:190'],
            'business_phone' => ['required', 'string', 'max:30'],
            'business_email' => ['required', 'email', 'max:190'],
            'business_address' => ['required', 'string', 'max:400'],
            'map_embed_url' => ['nullable', 'string', 'max:1000', 'starts_with:https://www.google.com/maps/embed,https://maps.google.com'],
            'facebook_url' => ['nullable', 'url', 'max:400'],
            'instagram_url' => ['nullable', 'url', 'max:400'],
            'tiktok_url' => ['nullable', 'url', 'max:400'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,svg', 'max:1024'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->images->store($request->file('logo'), 'branding', $this->settings->get('logo_path'));
        }
        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $this->images->store($request->file('favicon'), 'branding', $this->settings->get('favicon_path'));
        }

        unset($data['logo'], $data['favicon']);
        $this->settings->setMany($data, 'business');
    }

    private function updateOrdering(Request $request): void
    {
        $data = $request->validate([
            'ordering_enabled' => ['required', 'boolean'],
            'preordering_enabled' => ['required', 'boolean'],
            'min_order_amount' => ['required', 'numeric', 'min:0', 'max:1000'],
            'pickup_interval_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'pickup_lead_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'advance_order_days' => ['required', 'integer', 'min:0', 'max:30'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:30'],
            'currency' => ['required', 'string', 'size:3'],
            'order_notification_email' => ['nullable', 'email', 'max:190'],
        ]);

        $this->settings->setMany($data, 'ordering');
    }

    private function updateSeo(Request $request): void
    {
        $data = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('og_image')) {
            $data['og_image_path'] = $this->images->store($request->file('og_image'), 'branding', $this->settings->get('og_image_path'));
        }

        unset($data['og_image']);
        $this->settings->setMany($data, 'seo');
    }

    private function updateAnnouncement(Request $request): void
    {
        $data = $request->validate([
            'announcement_enabled' => ['required', 'boolean'],
            'announcement_text' => ['nullable', 'string', 'max:300'],
            'maintenance_mode' => ['required', 'boolean'],
        ]);

        $this->settings->setMany($data, 'announcement');
    }
}
