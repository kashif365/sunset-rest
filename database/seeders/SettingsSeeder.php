<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'business' => [
                'business_name' => 'Sunset Bagel Exchange',
                'business_phone' => '(732) 361-8119',
                'business_email' => 'sunsetbagelexchange@gmail.com',
                'business_address' => '3316 Sunset Ave., Ocean, NJ 07712',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3033.0!2d-74.052!3d40.145!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA4JzM2LjAiTiA3NMKwMDMnMDcuMiJX!5e0!3m2!1sen!2sus!4v0',
                'facebook_url' => 'https://www.facebook.com/',
                'instagram_url' => 'https://www.instagram.com/',
                'tiktok_url' => null,
                'logo_path' => null,
                'favicon_path' => null,
            ],
            'ordering' => [
                'ordering_enabled' => '1',
                'preordering_enabled' => '1',
                'min_order_amount' => '0',
                'pickup_interval_minutes' => '15',
                'pickup_lead_minutes' => '20',
                'advance_order_days' => '5',
                'tax_rate' => '6.625',
                'currency' => 'USD',
                'order_notification_email' => 'sunsetbagelexchange@gmail.com',
            ],
            'seo' => [
                'seo_title' => 'Sunset Bagel Exchange — Hand Rolled Bagels, Breakfast & Lunch in Ocean, NJ',
                'seo_description' => 'Hand rolled, kettle boiled, old fashioned bagels. Breakfast, lunch, coffee and catering in Ocean Township, NJ. Order online for pickup.',
                'seo_keywords' => 'bagels, Ocean NJ, breakfast, lunch, catering, hand rolled bagels, kettle boiled bagels',
                'og_image_path' => null,
            ],
            'announcement' => [
                'announcement_enabled' => '1',
                'announcement_text' => 'Ask about our Tacos Menu — available after 12:00 PM, Monday to Saturday!',
                'maintenance_mode' => '0',
            ],
        ];

        foreach ($settings as $group => $values) {
            foreach ($values as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
            }
        }
    }
}
