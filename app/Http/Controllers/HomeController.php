<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GalleryImage;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Services\PickupSlotService;

class HomeController extends Controller
{
    public function index(PickupSlotService $slots)
    {
        $featuredItems = MenuItem::with(['category', 'dietaryLabels', 'allergens', 'variations'])
            ->withCount('modifierGroups')
            ->available()
            ->where(fn ($q) => $q->where('is_featured', true)->orWhere('is_bestseller', true))
            ->orderByDesc('is_bestseller')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('home', [
            'slides' => HeroSlide::active()->get(),
            'featuredCategories' => Category::active()->where('is_featured', true)->ordered()->take(6)->get(),
            'featuredItems' => $featuredItems,
            'offers' => Promotion::offers()->current()->take(3)->get(),
            'banner' => Promotion::banners()->current()->first(),
            'gallery' => GalleryImage::active()->take(8)->get(),
            'hours' => \App\Models\BusinessHour::orderBy('day_of_week')->get(),
            'isOpenNow' => $slots->isOpenNow(),
        ]);
    }
}
