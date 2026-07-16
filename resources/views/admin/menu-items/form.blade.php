@extends('layouts.admin')

@section('title', $item->exists ? 'Edit Item: '.$item->name : 'New Menu Item')

@section('content')
    <form action="{{ $item->exists ? route('admin.menu-items.update', $item) : route('admin.menu-items.store') }}"
          method="post" enctype="multipart/form-data" class="row g-3">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <div class="col-12 col-xl-8">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Basics</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="name">Name *</label>
                            <input class="form-control" id="name" name="name" data-slug-source="#slug" value="{{ old('name', $item->name) }}" required maxlength="190">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="category_id">Category *</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="slug">Slug *</label>
                            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $item->slug) }}" required maxlength="190">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="price">Price ($) *</label>
                            <input class="form-control" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $item->price) }}" required inputmode="decimal">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="discounted_price">Discounted price ($)</label>
                            <input class="form-control" id="discounted_price" name="discounted_price" type="number" step="0.01" min="0" value="{{ old('discounted_price', $item->discounted_price) }}" inputmode="decimal">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="short_description">Short description (cards)</label>
                            <input class="form-control" id="short_description" name="short_description" value="{{ old('short_description', $item->short_description) }}" maxlength="500">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Full description (detail page)</label>
                            <textarea class="form-control" id="description" name="description" rows="3" maxlength="5000">{{ old('description', $item->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Variations</strong> <span class="text-body-secondary small">(sizes / serving options with their own price, e.g. Sandwich vs Per Lb)</span></div>
                <div class="card-body" x-ignore>
                    <div id="variations-wrap">
                        @php $variationRows = old('variations', $item->variations->map(fn ($v) => ['id' => $v->id, 'name' => $v->name, 'price' => $v->price])->all()); @endphp
                        @foreach($variationRows as $i => $row)
                            <div class="row g-2 mb-2 variation-row">
                                <input type="hidden" name="variations[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
                                <div class="col-6">
                                    <label class="visually-hidden" for="variation-name-{{ $i }}">Variation name</label>
                                    <input id="variation-name-{{ $i }}" class="form-control" name="variations[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="e.g. Per Lb" maxlength="120">
                                </div>
                                <div class="col-4">
                                    <label class="visually-hidden" for="variation-price-{{ $i }}">Variation price</label>
                                    <input id="variation-price-{{ $i }}" class="form-control" name="variations[{{ $i }}][price]" type="number" step="0.01" min="0" value="{{ $row['price'] ?? '' }}" placeholder="0.00">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.variation-row').remove()" aria-label="Remove variation">×</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-variation">+ Add variation</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Modifier groups</strong> <span class="text-body-secondary small">(bagel type, cream cheese, meats, cheeses, add-ons…)</span></div>
                <div class="card-body">
                    @if($modifierGroups->isEmpty())
                        <p class="text-body-secondary mb-0">No modifier groups yet — <a href="{{ route('admin.modifier-groups.create') }}">create one first</a>.</p>
                    @endif
                    <div class="row">
                        @foreach($modifierGroups as $group)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="modifier_groups[]" id="mg-{{ $group->id }}"
                                           value="{{ $group->id }}" @checked(in_array($group->id, old('modifier_groups', $item->modifierGroups->pluck('id')->all())))>
                                    <label class="form-check-label" for="mg-{{ $group->id }}">
                                        {{ $group->name }}
                                        <span class="text-body-secondary small">({{ $group->selection_type }}{{ $group->is_required ? ', required' : '' }})</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Dietary labels &amp; allergens</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="fw-bold small text-uppercase mb-2">Dietary labels</p>
                            @foreach($dietaryLabels as $label)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="dietary_labels[]" id="dl-{{ $label->id }}"
                                           value="{{ $label->id }}" @checked(in_array($label->id, old('dietary_labels', $item->dietaryLabels->pluck('id')->all())))>
                                    <label class="form-check-label" for="dl-{{ $label->id }}">{{ $label->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <p class="fw-bold small text-uppercase mb-2">Allergens</p>
                            @foreach($allergens as $allergen)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="allergens[]" id="al-{{ $allergen->id }}"
                                           value="{{ $allergen->id }}" @checked(in_array($allergen->id, old('allergens', $item->allergens->pluck('id')->all())))>
                                    <label class="form-check-label" for="al-{{ $allergen->id }}">{{ $allergen->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Availability window</strong> <span class="text-body-secondary small">(optional — e.g. breakfast only, weekend only)</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="available_from">From (time)</label>
                            <input class="form-control" id="available_from" name="available_from" type="time" value="{{ old('available_from', $item->available_from ? substr($item->available_from, 0, 5) : '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="available_until">Until (time)</label>
                            <input class="form-control" id="available_until" name="available_until" type="time" value="{{ old('available_until', $item->available_until ? substr($item->available_until, 0, 5) : '') }}">
                        </div>
                        <div class="col-md-6">
                            <p class="form-label mb-1">Days available <span class="text-body-secondary">(none = every day)</span></p>
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayIndex => $dayName)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="available_days[]" id="day-{{ $dayIndex }}"
                                           value="{{ $dayIndex }}" @checked(in_array($dayIndex, array_map('intval', old('available_days', $item->available_days ?? []))))>
                                    <label class="form-check-label" for="day-{{ $dayIndex }}">{{ $dayName }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>SEO</strong></div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="seo_title">SEO title</label>
                        <input class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title', $item->seo_title) }}" maxlength="190">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="meta_description">Meta description</label>
                        <input class="form-control" id="meta_description" name="meta_description" value="{{ old('meta_description', $item->meta_description) }}" maxlength="500">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Image</strong></div>
                <div class="card-body">
                    <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-preview="#item-image-preview" aria-label="Item image">
                    <img id="item-image-preview" class="img-fluid rounded mt-2" style="max-height: 160px;" src="{{ \App\Services\ImageService::thumbUrl($item->image) }}" alt="Menu item preview">
                    <label class="form-label mt-2" for="image_alt">Alt text</label>
                    <input class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $item->image_alt) }}" maxlength="190">
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Status &amp; flags</strong></div>
                <div class="card-body">
                    @foreach([
                        'is_available' => ['Available on site', $item->exists ? $item->is_available : true],
                        'is_sold_out' => ['Sold out', $item->is_sold_out],
                        'is_featured' => ['Featured', $item->is_featured],
                        'is_bestseller' => ['Bestseller', $item->is_bestseller],
                        'needs_verification' => ['Needs price/details verification', $item->needs_verification],
                    ] as $flag => [$label, $default])
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="{{ $flag }}" value="0">
                            <input class="form-check-input" type="checkbox" id="{{ $flag }}" name="{{ $flag }}" value="1" @checked(old($flag, $default))>
                            <label class="form-check-label" for="{{ $flag }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Stock &amp; prep</strong></div>
                <div class="card-body row g-3">
                    <div class="col-6">
                        <label class="form-label" for="stock_quantity">Stock qty <span class="text-body-secondary">(blank = untracked)</span></label>
                        <input class="form-control" id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $item->stock_quantity) }}" inputmode="numeric">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="low_stock_threshold">Low-stock alert at</label>
                        <input class="form-control" id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 5) }}" inputmode="numeric">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="prep_time_minutes">Prep time (min)</label>
                        <input class="form-control" id="prep_time_minutes" name="prep_time_minutes" type="number" min="0" value="{{ old('prep_time_minutes', $item->prep_time_minutes) }}" inputmode="numeric">
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="sort_order">Sort order</label>
                        <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" inputmode="numeric">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary w-100" type="submit">{{ $item->exists ? 'Save Changes' : 'Create Item' }}</button>
            <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('admin.menu-items.index') }}">Back to list</a>
        </div>
    </form>

    <template id="variation-template">
        <div class="row g-2 mb-2 variation-row">
            <input type="hidden" name="variations[__i__][id]" value="">
            <div class="col-6"><input class="form-control" name="variations[__i__][name]" placeholder="e.g. Per Lb" maxlength="120" aria-label="Variation name"></div>
            <div class="col-4"><input class="form-control" name="variations[__i__][price]" type="number" step="0.01" min="0" placeholder="0.00" aria-label="Variation price"></div>
            <div class="col-2"><button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.variation-row').remove()" aria-label="Remove variation">×</button></div>
        </div>
    </template>

    @push('scripts')@endpush
    <script>
        document.getElementById('add-variation')?.addEventListener('click', () => {
            const wrap = document.getElementById('variations-wrap');
            const index = Date.now();
            const html = document.getElementById('variation-template').innerHTML.replaceAll('__i__', index);
            wrap.insertAdjacentHTML('beforeend', html);
        });
    </script>
@endsection
