<?php

namespace Database\Seeders;

use App\Models\NavigationLink;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $header = [
            ['Menu', '/menu'],
            ['Order Online', '/menu'],
            ['Catering', '/catering'],
            ['About', '/about-us'],
            ['Contact', '/contact'],
        ];

        foreach ($header as $index => [$label, $url]) {
            NavigationLink::updateOrCreate(
                ['location' => 'header', 'label' => $label],
                ['url' => $url, 'new_tab' => false, 'is_active' => true, 'sort_order' => $index]
            );
        }

        $footer = [
            ['Full Menu', '/menu'],
            ['Catering', '/catering'],
            ['About Us', '/about-us'],
            ['Contact & Location', '/contact'],
            ['FAQ', '/faq'],
        ];

        foreach ($footer as $index => [$label, $url]) {
            NavigationLink::updateOrCreate(
                ['location' => 'footer', 'label' => $label],
                ['url' => $url, 'new_tab' => false, 'is_active' => true, 'sort_order' => $index]
            );
        }
    }
}
