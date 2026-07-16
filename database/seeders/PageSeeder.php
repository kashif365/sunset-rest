<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'About Us',
            'content' => '<p>Sunset Bagel Exchange is Ocean Township\'s neighborhood bagel shop. Every morning our bakers roll the dough by hand and boil each bagel in the kettle before baking — the old-fashioned way that gives a real bagel its shiny crust and chewy bite.</p><p>From two-egg breakfast sandwiches to stacked Boar\'s Head lunch classics, homemade salads and a little taste of Latin culture, everything is made fresh to order.</p>',
            'is_active' => true,
            'seo_title' => 'About Us — Sunset Bagel Exchange',
            'meta_description' => 'The story behind Sunset Bagel Exchange in Ocean, NJ.',
        ]);

        Page::updateOrCreate(['slug' => 'catering'], [
            'title' => 'Catering',
            'content' => '<p>Delicious breakfast and lunch options for your family gathering or business meeting. Sales rep? Let us help make your meeting a success!</p>',
            'is_active' => true,
        ]);

        Page::updateOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'content' => $this->privacyPolicy(),
            'is_active' => true,
            'seo_title' => 'Privacy Policy — Sunset Bagel Exchange',
        ]);

        Page::updateOrCreate(['slug' => 'terms-and-conditions'], [
            'title' => 'Terms and Conditions',
            'content' => $this->terms(),
            'is_active' => true,
            'seo_title' => 'Terms and Conditions — Sunset Bagel Exchange',
        ]);
    }

    private function privacyPolicy(): string
    {
        return <<<'HTML'
<p>This Privacy Policy explains how Sunset Bagel Exchange ("we", "us") collects, uses and protects information when you use our website and online ordering system.</p>
<h2>Information We Collect</h2>
<p>When you place an order, contact us, or sign up for our email list, we collect your name, email address, phone number and order details. We do not collect or store payment card information — orders are settled in person or by phone.</p>
<h2>How We Use Information</h2>
<p>We use your information to process orders, communicate order status, respond to inquiries, and — if you opt in — send occasional news and offers. We never sell your personal information.</p>
<h2>Cookies</h2>
<p>Our website uses a session cookie to remember your shopping cart. No third-party tracking or advertising cookies are used.</p>
<h2>Contact Us</h2>
<p>Questions about this policy can be sent to sunsetbagelexchange@gmail.com.</p>
HTML;
    }

    private function terms(): string
    {
        return <<<'HTML'
<p>By using this website and placing an order with Sunset Bagel Exchange, you agree to the following terms.</p>
<h2>Ordering &amp; Pickup</h2>
<p>Online orders are for pickup only at our Ocean, NJ location. Orders are prepared for the pickup date and time you select at checkout. Please arrive on time — the kitchen prepares food fresh to order.</p>
<h2>Payment</h2>
<p>Unless online payment has been enabled, orders are paid for at pickup (cash or card) or confirmed by phone. Prices are subject to change without notice.</p>
<h2>Cancellations</h2>
<p>To cancel or change an order, please call us directly as soon as possible at (732) 361-8119.</p>
<h2>Allergen Notice</h2>
<p>Our kitchen prepares food that may contain milk, eggs, wheat, peanuts and tree nuts. Please inform us of any allergies before ordering.</p>
HTML;
    }
}
