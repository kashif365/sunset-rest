<?php

namespace App\Support;

use App\Models\BusinessHour;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Services\ImageService;
use App\Services\SettingsService;

/** JSON-LD builders for schema.org structured data. */
class StructuredData
{
    public function __construct(private readonly SettingsService $settings) {}

    public function restaurant(): array
    {
        $hours = BusinessHour::orderBy('day_of_week')->get()
            ->where('is_closed', false)
            ->map(fn ($h) => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $h->dayName(),
                'opens' => substr((string) $h->open_time, 0, 5),
                'closes' => substr((string) $h->close_time, 0, 5),
            ])->values()->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => $this->settings->get('business_name', 'Sunset Bagel Exchange'),
            'servesCuisine' => ['Bagels', 'Breakfast', 'American', 'Coffee'],
            'url' => route('home'),
            'telephone' => $this->settings->get('business_phone', '(732) 361-8119'),
            'email' => $this->settings->get('business_email', 'sunsetbagelexchange@gmail.com'),
            'priceRange' => '$$',
            'image' => ImageService::url($this->settings->get('og_image_path'), '/images/og-default.svg'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '3316 Sunset Ave.',
                'addressLocality' => 'Ocean',
                'addressRegion' => 'NJ',
                'postalCode' => '07712',
                'addressCountry' => 'US',
            ],
            'hasMenu' => route('menu.index'),
            'openingHoursSpecification' => $hours,
            'acceptsReservations' => false,
        ];
    }

    public function menuItem(MenuItem $item): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $item->name,
            'description' => $item->short_description ?: $item->description,
            'image' => ImageService::url($item->image),
            'url' => route('menu.item', $item),
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format($item->effectivePrice(), 2, '.', ''),
                'priceCurrency' => strtoupper($this->settings->get('currency', 'USD')),
                'availability' => $item->isOrderable()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];

        return $data;
    }

    /** @param array<string, string> $trail label => url */
    public function breadcrumbs(array $trail): array
    {
        $items = [];
        $position = 1;
        foreach ($trail as $label => $url) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $label,
                'item' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public function faqPage(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => Faq::active()->get()->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ])->values()->all(),
        ];
    }
}
