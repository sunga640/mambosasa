@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\HotelBranch> $branches */
    $readOnly = (bool) ($readOnly ?? false);
@endphp
@if ($branches->isEmpty())
    <p class="text-15" style="opacity:.8;">{{ __('Property information will appear here once branches are added in the admin area.') }}</p>
@else
    <div class="row y-gap-30 x-gap-30 properties-directory-grid">
        @foreach ($branches as $branch)
            <div class="col-lg-6">
                <article class="properties-directory-card rounded-16 overflow-hidden h-full" style="border:1px solid rgba(18,34,35,.12);background:#fff;box-shadow:0 4px 24px rgba(18,34,35,.06);">
                    <div class="px-30 py-30">
                        <h2 class="properties-directory-card__title text-26 fw-600 mb-10" style="font-family:'Cormorant Garamond',Georgia,serif;">{{ $branch->name }}</h2>
                        @if ($branch->city || $branch->location_address || $branch->country)
                            <p class="text-14 mb-15" style="opacity:.72;line-height:1.55;color:#555;">
                                {{ collect([$branch->location_address, $branch->city, $branch->country])->filter()->implode(' · ') }}
                            </p>
                        @endif
                        <p class="text-13 mb-15" style="opacity:.7;">
                            {{ trans_choice(':count room|:count rooms', $branch->rooms_count, ['count' => $branch->rooms_count]) }}
                        </p>
                        <div class="text-13 mb-10" style="opacity:.8;">
                            <strong>{{ __('Floors') }}:</strong> {{ (int) ($branch->floors_count ?? 1) }}
                            <span style="margin:0 .5rem;opacity:.45;">|</span>
                            <strong>{{ __('Status') }}:</strong>
                            <span style="color:{{ $branch->is_active ? '#15803d' : '#b45309' }};">{{ $branch->is_active ? __('Active') : __('Inactive') }}</span>
                        </div>
                        <div class="properties-directory-card__contacts text-14" style="line-height:1.6;">
                            @if ($branch->contact_phone)
                                <div class="mb-5">
                                    <a href="tel:{{ preg_replace('/\s+/', '', $branch->contact_phone) }}" class="text-dark-1" style="text-decoration:none;">{{ $branch->contact_phone }}</a>
                                </div>
                            @endif
                            @if ($branch->contact_email)
                                <div>
                                    <a href="mailto:{{ $branch->contact_email }}" class="text-dark-1" style="text-decoration:none;word-break:break-word;">{{ $branch->contact_email }}</a>
                                </div>
                            @endif
                        </div>
                        @if ($branch->extra_notes)
                            <p class="text-14 mt-20 lh-16" style="opacity:.82;">{{ $branch->extra_notes }}</p>
                        @endif
                        @if ($readOnly)
                            <div class="mt-20" style="display:flex;gap:.65rem;flex-wrap:wrap;opacity:.5;cursor:not-allowed;">
                                <span>{{ __('Edit') }}</span>
                                <span>{{ __('Rooms') }}</span>
                                <span>{{ __('Delete') }}</span>
                            </div>
                        @else
                            <div class="mt-20" style="display:flex;gap:.65rem;flex-wrap:wrap;">
                                <a href="{{ route('admin.branches.edit', $branch) }}">{{ __('Edit') }}</a>
                                <a href="{{ route('admin.rooms.index', ['branch_id' => $branch->id]) }}">{{ __('Rooms') }}</a>
                                <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" data-swal-delete
                                      data-swal-title="{{ __('Delete property/branch?') }}"
                                      data-swal-text="{{ __('This will remove the branch and its related data.') }}"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border:none;background:none;color:#dc2626;padding:0;cursor:pointer;">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            </div>
        @endforeach
    </div>
@endif
