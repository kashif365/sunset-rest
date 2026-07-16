@php /** @var \App\Models\MenuItem $item */ @endphp
<article class="product-card"
         data-menu-item
         data-name="{{ $item->name }}"
         data-desc="{{ $item->short_description }}"
         data-diet="{{ $item->dietaryLabels->pluck('slug')->implode(',') }}"
         data-available="{{ $item->isOrderable() ? 1 : 0 }}">
    <div class="product-img-wrap">
        <a href="{{ route('menu.item', $item) }}" tabindex="-1" aria-hidden="true">
            <img src="{{ \App\Services\ImageService::thumbUrl($item->image) }}"
                 alt="{{ $item->image_alt ?: $item->name }}" loading="lazy" width="480" height="360">
        </a>
        <div class="badge-stack">
            @if($item->is_featured)
                <span class="badge badge-featured rounded-pill"><i class="bi bi-star-fill me-1" aria-hidden="true"></i>Featured</span>
            @endif
            @if($item->is_bestseller)
                <span class="badge badge-bestseller rounded-pill"><i class="bi bi-fire me-1" aria-hidden="true"></i>Bestseller</span>
            @endif
            @if($item->is_sold_out || ($item->stock_quantity !== null && $item->stock_quantity <= 0))
                <span class="badge badge-soldout rounded-pill">Sold Out</span>
            @endif
            @if($item->hasDiscount())
                <span class="badge text-bg-danger rounded-pill">Special</span>
            @endif
        </div>
    </div>
    <div class="card-body d-flex flex-column p-3">
        <h3 class="product-name h6 mb-1">
            <a href="{{ route('menu.item', $item) }}">{{ $item->name }}</a>
        </h3>
        @if($item->short_description)
            <p class="product-desc mb-2">{{ $item->short_description }}</p>
        @endif

        @if($item->dietaryLabels->isNotEmpty() || $item->allergens->isNotEmpty())
            <div class="d-flex flex-wrap gap-1 mb-2">
                @foreach($item->dietaryLabels as $label)
                    <span class="badge badge-diet rounded-pill">@if($label->icon)<i class="bi {{ $label->icon }} me-1" aria-hidden="true"></i>@endif{{ $label->name }}</span>
                @endforeach
                @foreach($item->allergens as $allergen)
                    <span class="badge badge-allergen rounded-pill" title="Contains {{ strtolower($allergen->name) }}">{{ $allergen->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto d-flex align-items-center justify-content-between gap-2">
            <span class="price">
                @if($item->hasDiscount())
                    <span class="price-old">${{ number_format((float) $item->price, 2) }}</span>
                @endif
                ${{ number_format($item->effectivePrice(), 2) }}
                @if($item->variations->isNotEmpty())<span class="small text-muted fw-normal">+</span>@endif
            </span>
            @if($item->isOrderable())
                @if($item->variations->isNotEmpty() || ($item->modifier_groups_count ?? 0) > 0)
                    <a href="{{ route('menu.item', $item) }}" class="btn btn-brand btn-sm">Customize</a>
                @else
                    <form action="{{ route('cart.add', $item) }}" method="post">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-brand btn-sm">
                            <i class="bi bi-bag-plus me-1" aria-hidden="true"></i>Add to Cart
                        </button>
                    </form>
                @endif
            @else
                <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Unavailable</button>
            @endif
        </div>
    </div>
</article>
