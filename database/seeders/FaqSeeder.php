<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['Do you take online orders for pickup?', 'Yes! Browse our full menu, add items to your cart, and choose a pickup date and time at checkout. You can pay in store or by phone — online card payment is coming soon.'],
            ['How far in advance can I order?', 'You can place an order for today (subject to kitchen lead time) or up to several days ahead. The exact window is shown when you choose your pickup date at checkout.'],
            ['Do you offer catering?', 'Yes — we cater breakfast and lunch spreads for family gatherings and business meetings. Visit our Catering page or contact us for a custom quote.'],
            ['Can I customize my bagel or sandwich?', 'Absolutely. Most items let you choose your bagel flavor, cream cheese, meat, cheese and add-ons right from the product page.'],
            ['Do you have gluten-free or allergy-friendly options?', 'We prepare food in a kitchen that handles milk, eggs, wheat, peanuts and tree nuts. Please review the allergen badges on each menu item, and let us know about any allergies when ordering.'],
            ['What are your hours?', 'See our Hours & Location on the homepage and Contact page — they\'re always kept current there.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], [
                'answer' => $answer, 'is_active' => true, 'sort_order' => $index,
            ]);
        }
    }
}
