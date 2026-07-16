<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_creates_a_submission(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '732-555-0000',
            'subject' => 'Catering question',
            'message' => 'Do you cater for 20 people?',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'john@example.com',
            'subject' => 'Catering question',
        ]);
    }

    public function test_contact_form_requires_name_email_and_message(): void
    {
        $response = $this->post(route('contact.submit'), []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_contact_form_honeypot_field_blocks_bots(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Buy cheap watches',
            'website' => 'http://spam.example.com',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_subscribing_to_the_newsletter(): void
    {
        $response = $this->post(route('subscribe'), ['email' => 'fan@example.com']);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscribers', ['email' => 'fan@example.com']);
    }

    public function test_subscribing_twice_does_not_duplicate(): void
    {
        $this->post(route('subscribe'), ['email' => 'fan@example.com']);
        $this->post(route('subscribe'), ['email' => 'fan@example.com']);

        $this->assertSame(1, \App\Models\Subscriber::where('email', 'fan@example.com')->count());
    }
}
