@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\HotelBranch> $branches */
    $readOnly = (bool) ($readOnly ?? false);
@endphp
@if ($branches->isEmpty())
    <p class="text-15" style="opacity:.8;">{{ __('Property information will appear here once branches are added in the admin area.') }}</p>
@else
    <div class="properties-directory-grid">
        @foreach ($branches as $branch)
            @php
                $branchImage = collect($branch->preview_images ?? [])->filter()->first() ?: $branch->logo_path;
                $branchImageUrl = $branchImage
                    ? (str_starts_with((string) $branchImage, 'http')
                        ? $branchImage
                        : \App\Support\PublicDisk::url((string) $branchImage))
                    : asset('img/pageHero/4.png');
            @endphp
            <article class="properties-directory-card">
                <div class="properties-directory-card__media" style="background-image:url('{{ $branchImageUrl }}');"></div>
                <div class="properties-directory-card__body">
                    <span class="properties-directory-card__tag">{{ $branch->city ?: __('Branch') }}</span>
                    <h2 class="properties-directory-card__title">{{ $branch->name }}</h2>
                    @if ($branch->city || $branch->location_address || $branch->country)
                        <p class="properties-directory-card__location">
                            {{ collect([$branch->location_address, $branch->city, $branch->country])->filter()->implode(' | ') }}
                        </p>
                    @endif

                    <div class="properties-directory-card__meta">
                        <span>{{ trans_choice(':count room|:count rooms', $branch->rooms_count, ['count' => $branch->rooms_count]) }}</span>
                        <span>{{ __('Floors: :count', ['count' => (int) ($branch->floors_count ?? 1)]) }}</span>
                        <span>{{ $branch->is_active ? __('Active') : __('Inactive') }}</span>
                    </div>

                    <div class="properties-directory-card__contacts">
                        @if ($branch->contact_phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $branch->contact_phone) }}">{{ $branch->contact_phone }}</a>
                        @endif
                        @if ($branch->contact_email)
                            <a href="mailto:{{ $branch->contact_email }}">{{ $branch->contact_email }}</a>
                        @endif
                    </div>

                    @if ($branch->extra_notes)
                        <p class="properties-directory-card__notes">{{ $branch->extra_notes }}</p>
                    @endif

                    @if ($readOnly)
                        <div class="properties-directory-card__actions is-readonly">
                            <span>{{ __('View property details') }}</span>
                        </div>
                    @else
                        <div class="properties-directory-card__actions">
                            <a href="{{ route('admin.branches.edit', $branch) }}">{{ __('Edit') }}</a>
                            <a href="{{ route('admin.rooms.index', ['branch_id' => $branch->id]) }}">{{ __('Rooms') }}</a>
                            <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" data-swal-delete
                                  data-swal-title="{{ __('Delete property/branch?') }}"
                                  data-swal-text="{{ __('This will remove the branch and its related data.') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <style>
        .properties-directory-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .properties-directory-card {
            display: grid;
            grid-template-columns: minmax(220px, 280px) minmax(0, 1fr);
            min-height: 20rem;
            border: 1px solid rgba(18, 34, 35, 0.12);
            background: #fff;
            box-shadow: 0 10px 28px rgba(18, 34, 35, 0.06);
            overflow: hidden;
        }
        .properties-directory-card__media {
            min-height: 100%;
            background-size: cover;
            background-position: center;
        }
        .properties-directory-card__body {
            padding: 1.45rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            justify-content: center;
        }
        .properties-directory-card__tag {
            display: inline-flex;
            width: fit-content;
            padding: 0.42rem 0.7rem;
            background: #f7f2e8;
            color: #8a6a39;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }
        .properties-directory-card__title {
            margin: 0;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.8rem, 3vw, 2.45rem);
            line-height: 0.98;
            color: #17352f;
        }
        .properties-directory-card__location,
        .properties-directory-card__notes {
            margin: 0;
            color: #5c6b6d;
            line-height: 1.7;
        }
        .properties-directory-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }
        .properties-directory-card__meta span {
            padding: 0.45rem 0.7rem;
            background: #f8fafc;
            color: #334155;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .properties-directory-card__contacts {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .properties-directory-card__contacts a,
        .properties-directory-card__actions a,
        .properties-directory-card__actions button,
        .properties-directory-card__actions span {
            color: #17352f;
            text-decoration: none;
            font-weight: 600;
        }
        .properties-directory-card__actions {
            margin-top: 0.4rem;
            display: flex;
            gap: 0.9rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .properties-directory-card__actions button {
            border: none;
            background: none;
            padding: 0;
            cursor: pointer;
            color: #b91c1c;
        }
        .properties-directory-card__actions.is-readonly {
            opacity: 0.68;
        }
        @media (max-width: 991px) {
            .properties-directory-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 767px) {
            .properties-directory-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .properties-directory-card__media {
                min-height: 14rem;
            }
        }
    </style>
@endif
