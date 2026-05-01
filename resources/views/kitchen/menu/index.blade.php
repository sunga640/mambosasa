@extends('layouts.kitchen')

@section('title', __('Kitchen Menu'))

@section('content')
    <div class="k-grid">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Menu Catalog') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Maintain the dish list guests see after scanning room QR codes or ordering from their stay portal.') }}</p>
        </div>

        <section class="k-card">
            <div class="k-toolbar">
                <div>
                    <h2 class="k-form-section__title">{{ __('Add menu item') }}</h2>
                    <p class="k-form-section__copy">{{ __('Compact builder for dish name, price, prep time, and visibility.') }}</p>
                </div>
                <div class="text-13 k-muted">{{ __('Catalogue pages: :count per page', ['count' => 12]) }}</div>
            </div>
            <form method="POST" action="{{ route('kitchen.menu.store') }}" enctype="multipart/form-data" class="k-form-section">
                @csrf
                <div class="k-form-grid" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">
                    <div class="k-field"><label>{{ __('Dish name') }}</label><input type="text" name="name" placeholder="{{ __('e.g. Swahili fish curry') }}" required></div>
                    <div class="k-field"><label>{{ __('Price') }}</label><input type="number" name="price" min="0" step="0.01" inputmode="decimal" placeholder="18000" required></div>
                    <div class="k-field"><label>{{ __('Prep minutes') }}</label><input type="number" name="preparation_minutes" min="1" max="240" value="25" required></div>
                    <div class="k-field"><label>{{ __('Sort order') }}</label><input type="number" name="sort_order" min="0" value="0"></div>
                    <div class="k-field"><label>{{ __('Upload image') }}</label><input type="file" name="image" accept="image/*"></div>
                    <div class="k-checkbox"><input type="checkbox" id="kitchen-menu-active" name="is_active" value="1" checked><label for="kitchen-menu-active">{{ __('Available now') }}</label></div>
                </div>
                <div class="k-field k-field--span"><label>{{ __('Description') }}</label><textarea name="description" rows="2" placeholder="{{ __('Describe ingredients, serving style, or special notes for the guest view.') }}"></textarea></div>
                @include('partials.media-picker', [
                    'assets' => $mediaAssets,
                    'name' => 'media_asset_id',
                    'label' => __('Or choose from media gallery'),
                    'selected' => old('media_asset_id'),
                ])
                <div><button class="dash-btn dash-btn--primary" type="submit">{{ __('Save dish') }}</button></div>
            </form>
        </section>

        <section class="k-card">
            <div class="k-toolbar">
                <div>
                    <h2>{{ __('Current menu') }}</h2>
                    <p class="text-13 k-muted" style="margin:0;">{{ __('Search quickly through visible and hidden dishes before editing them.') }}</p>
                </div>
                <div class="k-search">
                    <div class="k-field">
                        <label>{{ __('Search dishes') }}</label>
                        <input type="search" id="kitchenMenuSearch" placeholder="{{ __('Search by dish name or description') }}">
                    </div>
                    <div class="k-field">
                        <label>{{ __('Visibility') }}</label>
                        <select id="kitchenMenuStatusFilter">
                            <option value="all">{{ __('All dishes') }}</option>
                            <option value="active">{{ __('Active only') }}</option>
                            <option value="hidden">{{ __('Hidden only') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="k-grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
                @foreach ($items as $item)
                    <article class="k-card js-kitchen-menu-card" data-name="{{ \Illuminate\Support\Str::lower($item->name) }}" data-description="{{ \Illuminate\Support\Str::lower((string) $item->description) }}" data-status="{{ $item->is_active ? 'active' : 'hidden' }}" style="background:var(--brand-theme-surface-card);">
                        @if ($item->image_path)
                            <img src="{{ \App\Support\PublicDisk::url($item->image_path) }}" alt="{{ $item->name }}" loading="lazy" style="width:100%;height:180px;object-fit:cover;margin-bottom:.85rem;">
                        @endif
                        <div class="k-actions" style="justify-content:space-between;">
                            <h3 style="margin-bottom:.25rem;">{{ $item->name }}</h3>
                            <span class="text-13 {{ $item->is_active ? '' : 'k-muted' }}">{{ $item->is_active ? __('Active') : __('Hidden') }}</span>
                        </div>
                            <p class="text-13 k-muted" style="min-height:38px;">{{ \Illuminate\Support\Str::limit((string) $item->description, 92) }}</p>
                        <div class="k-actions text-13" style="justify-content:space-between;">
                            <span>{{ number_format((float) $item->price, 0) }} TZS</span>
                            <span>{{ $item->preparation_minutes }} {{ __('mins') }}</span>
                        </div>
                        <form method="POST" action="{{ route('kitchen.menu.update', $item) }}" enctype="multipart/form-data" class="k-form-section mt-15">
                            @csrf
                            @method('PUT')
                            <div class="k-field"><label>{{ __('Dish name') }}</label><input type="text" name="name" value="{{ $item->name }}" required></div>
                            <div class="k-field"><label>{{ __('Description') }}</label><textarea name="description" rows="2">{{ $item->description }}</textarea></div>
                            <div class="k-form-grid" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));">
                                <div class="k-field"><label>{{ __('Price') }}</label><input type="number" name="price" min="0" step="0.01" inputmode="decimal" value="{{ $item->price }}" required></div>
                                <div class="k-field"><label>{{ __('Prep minutes') }}</label><input type="number" name="preparation_minutes" min="1" max="240" value="{{ $item->preparation_minutes }}" required></div>
                                <div class="k-field"><label>{{ __('Sort order') }}</label><input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}"></div>
                                <div class="k-field"><label>{{ __('Replace image') }}</label><input type="file" name="image" accept="image/*"></div>
                                <div class="k-checkbox"><input type="checkbox" id="menu-active-{{ $item->id }}" name="is_active" value="1" @checked($item->is_active)><label for="menu-active-{{ $item->id }}">{{ __('Available now') }}</label></div>
                            </div>
                            @include('partials.media-picker', [
                                'assets' => $mediaAssets,
                                'name' => 'media_asset_id',
                                'label' => __('Replace from media gallery'),
                                'selected' => old('media_asset_id', $mediaAssets->firstWhere('path', $item->image_path)?->id),
                            ])
                            <button class="dash-btn dash-btn--primary" type="submit">{{ __('Update dish') }}</button>
                        </form>
                        <form method="POST" action="{{ route('kitchen.menu.destroy', $item) }}" onsubmit="return confirm(@json(__('Delete this menu item completely?')));">
                            @csrf
                            @method('DELETE')
                            <button class="dash-btn dash-btn--ghost" type="submit" style="width:100%;">{{ __('Delete dish') }}</button>
                        </form>
                    </article>
                @endforeach
            </div>
            <div class="mt-20">{{ $items->links() }}</div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var search = document.getElementById('kitchenMenuSearch');
        var status = document.getElementById('kitchenMenuStatusFilter');
        var cards = Array.from(document.querySelectorAll('.js-kitchen-menu-card'));
        if (!search || !status || cards.length === 0) return;

        function filterCards() {
            var term = (search.value || '').toLowerCase().trim();
            var state = status.value || 'all';
            cards.forEach(function (card) {
                var hay = (card.dataset.name || '') + ' ' + (card.dataset.description || '');
                var matchesTerm = term === '' || hay.indexOf(term) !== -1;
                var matchesStatus = state === 'all' || card.dataset.status === state;
                card.style.display = matchesTerm && matchesStatus ? '' : 'none';
            });
        }

        search.addEventListener('input', filterCards);
        status.addEventListener('change', filterCards);
    })();
</script>
@endpush
