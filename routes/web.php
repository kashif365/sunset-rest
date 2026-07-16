<?php

use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------
// Public site
// ---------------------------------------------------------------

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/category/{category}', [App\Http\Controllers\MenuController::class, 'category'])->name('menu.category');
Route::get('/menu/{menuItem}', [App\Http\Controllers\MenuController::class, 'show'])->name('menu.item');

// Cart
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{menuItem}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{line}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/coupon', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::delete('/cart/{line}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])
    ->middleware('throttle:public-forms')->name('cart.coupon.apply');

// Checkout / orders
Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])
    ->middleware('throttle:checkout')->name('checkout.store');
Route::get('/order/confirmation/{orderNumber}', [App\Http\Controllers\CheckoutController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/order/receipt/{orderNumber}', [App\Http\Controllers\CheckoutController::class, 'receipt'])->name('orders.receipt');

// Content pages
Route::get('/catering', [App\Http\Controllers\PageController::class, 'catering'])->name('catering');
Route::get('/about-us', [App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\PageController::class, 'submitContact'])
    ->middleware('throttle:public-forms')->name('contact.submit');
Route::get('/faq', [App\Http\Controllers\PageController::class, 'faq'])->name('faq');
Route::post('/subscribe', [App\Http\Controllers\SubscriberController::class, 'store'])
    ->middleware('throttle:public-forms')->name('subscribe');

// SEO
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Dynamic pages (privacy-policy, terms-and-conditions, ...).
Route::get('/p/{page}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// ---------------------------------------------------------------
// Admin panel
// ---------------------------------------------------------------

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login'])
            ->middleware('throttle:login')->name('login.attempt');
    });

    Route::middleware(['auth', 'admin.role'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Orders, customers and messages — all back-office roles.
        Route::get('orders/{order}/receipt', [App\Http\Controllers\Admin\OrderController::class, 'receipt'])->name('orders.receipt');
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::resource('contact-submissions', App\Http\Controllers\Admin\ContactSubmissionController::class)->only(['index', 'show', 'destroy']);

        // Content management — manager and super admin.
        Route::middleware('admin.role:super_admin,manager')->group(function () {
            Route::post('reorder/{type}', App\Http\Controllers\Admin\ReorderController::class)->name('reorder');

            Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except('show');
            Route::resource('menu-items', App\Http\Controllers\Admin\MenuItemController::class)->except('show');

            Route::post('modifier-options', [App\Http\Controllers\Admin\ModifierGroupController::class, 'storeOption'])->name('modifier-options.store');
            Route::put('modifier-options/{option}', [App\Http\Controllers\Admin\ModifierGroupController::class, 'updateOption'])->name('modifier-options.update');
            Route::delete('modifier-options/{option}', [App\Http\Controllers\Admin\ModifierGroupController::class, 'destroyOption'])->name('modifier-options.destroy');
            Route::resource('modifier-groups', App\Http\Controllers\Admin\ModifierGroupController::class)->except('show');

            Route::resource('dietary-labels', App\Http\Controllers\Admin\DietaryLabelController::class)->except('show');
            Route::resource('allergens', App\Http\Controllers\Admin\AllergenController::class)->except('show');
            Route::resource('hero-slides', App\Http\Controllers\Admin\HeroSlideController::class)->except('show');
            Route::resource('promotions', App\Http\Controllers\Admin\PromotionController::class)->except('show');
            Route::resource('catering-packages', App\Http\Controllers\Admin\CateringPackageController::class)->except('show');
            Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class)->except('show');
            Route::resource('gallery-images', App\Http\Controllers\Admin\GalleryImageController::class)->except('show');
            Route::resource('pages', App\Http\Controllers\Admin\PageController::class)->except('show');
            Route::resource('navigation-links', App\Http\Controllers\Admin\NavigationLinkController::class)->except('show');
            Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class)->except('show');

            Route::get('subscribers/export', [App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');
            Route::get('subscribers', [App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
            Route::patch('subscribers/{subscriber}/toggle', [App\Http\Controllers\Admin\SubscriberController::class, 'toggle'])->name('subscribers.toggle');
            Route::delete('subscribers/{subscriber}', [App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
        });

        // Settings, hours and users — super admin only.
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('hours', [App\Http\Controllers\Admin\BusinessHoursController::class, 'edit'])->name('hours.edit');
            Route::put('hours', [App\Http\Controllers\Admin\BusinessHoursController::class, 'update'])->name('hours.update');
            Route::post('hours/holidays', [App\Http\Controllers\Admin\BusinessHoursController::class, 'storeHoliday'])->name('hours.holidays.store');
            Route::delete('hours/holidays/{holidayHour}', [App\Http\Controllers\Admin\BusinessHoursController::class, 'destroyHoliday'])->name('hours.holidays.destroy');

            Route::get('settings/{tab?}', [App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings/{tab}', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

            Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except('show');
        });
    });
});
