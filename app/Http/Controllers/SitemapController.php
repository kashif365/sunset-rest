<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('menu.index'), 'priority' => '0.9'],
            ['loc' => route('catering'), 'priority' => '0.7'],
            ['loc' => route('about'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.6'],
            ['loc' => route('faq'), 'priority' => '0.5'],
        ]);

        $urls = $urls
            ->merge(Category::active()->get()->map(fn ($c) => [
                'loc' => route('menu.category', $c),
                'lastmod' => $c->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]))
            ->merge(MenuItem::available()->get()->map(fn ($i) => [
                'loc' => route('menu.item', $i),
                'lastmod' => $i->updated_at?->toAtomString(),
                'priority' => '0.7',
            ]))
            ->merge(Page::active()->get()->map(fn ($p) => [
                'loc' => route('page.show', $p),
                'lastmod' => $p->updated_at?->toAtomString(),
                'priority' => '0.4',
            ]));

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
