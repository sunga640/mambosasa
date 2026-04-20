@extends('layouts.site')

@section('title', __('Book your stay'))

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css"/>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<style>
  .booking-grid-container {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr 1fr;
    gap: 25px;
    align-items: start;
    margin-top: 30px;
  }

  /* 1. KUSHOTO: Picha & Description */
  .booking-media-col img#booking-main-img {
    width: 100%; height: 400px; object-fit: cover; border-radius: 12px;
  }
  .room-description-box {
    margin-top: 25px; padding: 20px; background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; border-left: 5px solid #2563eb;
  }
  .room-description-box p { color: #2c3e50 !important; font-size: 15px !important; line-height: 1.7 !important; opacity: 1 !important; }

  /* 2. KATIKATI: Room Selection & Calendar */
  .booking-selection-col { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
  .room-list-container { max-height: 220px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; margin-bottom: 20px; }

  .room-item-row { display: flex; align-items: center; padding: 12px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
  .room-item-row:hover { background: #f8fafc; }
  .room-item-row input[type="radio"] { margin-right: 12px; width: 18px; height: 18px; flex-shrink: 0; }
  .room-info-text { font-size: 14px; font-weight: 500; color: #333; }

  /* Calendar Colors (Booked vs Available) */
  .flatpickr-day.booking-day-available {
    background: #dcfce7 !important; border-color: #dcfce7 !important; color: #166534 !important;
  }
  .flatpickr-day.booking-day-booked {
    background: #fee2e2 !important; border-color: #fecaca !important; color: #b91c1c !important;
    opacity: 1 !important; cursor: not-allowed !important;
  }
  /* Inahakikisha tarehe zilizochaguliwa zinaonekana vizuri */
  .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
    background: #2563eb !important; color: white !important; border-color: #2563eb !important;
  }

  .summary-card { margin-top: 20px; padding: 15px; background: #f0f7ff; border-radius: 10px; border: 1px dashed #2563eb; }

  /* 3. KULIA: Form */
  .booking-form-col { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; }
  .custom-input-group { margin-bottom: 18px; }
  .custom-input-group label { display: block; font-size: 13px; margin-bottom: 6px; font-weight: 600; color: #333; }
  .custom-input-group input { width: 100%; height: 46px; border: 1px solid #d1d5db; border-radius: 8px; padding: 0 15px; font-size: 14px; }

  .payment-item { display: flex; align-items: center; padding: 12px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 8px; cursor: pointer; }
  .payment-item input { margin-right: 12px; width: 18px; height: 18px; }

  @media (max-width: 1100px) { .booking-grid-container { grid-template-columns: 1fr; } }
  /* Make intl-tel-input match your inputs */
.iti {
    width: 100%;
}

.iti input {
    width: 100%;
    height: 52px; /* adjust to match others */
    border-radius: 12px;
    border: 1px solid #d1d5db;
    padding-left: 95px !important;
    font-size: 15px;
    background: #fff;
}

/* Flag container alignment */
.iti__flag-container {
    padding-left: 12px;
}

/* Fix vertical alignment */
.iti--separate-dial-code .iti__selected-flag {
    height: 100%;
    border-radius: 12px 0 0 12px;
}

/* Match hover/focus style */
.iti input:focus {
    border-color: #2563eb;
    outline: none;
}
</style>
@endpush

@section('content')
@php
  $type = $selectedType;
  $typeThumbs = $type ? $type->thumbnailUrls() : [];
  $mainImage = $type?->heroImageUrl() ?? asset('img/roomsSingle/3/1.png');
  $price = $type ? (float) $type->price : 0;
  $defaultCheckIn = old('check_in', \Carbon\Carbon::today()->format('Y-m-d'));
  $defaultCheckOut = old('check_out', \Carbon\Carbon::tomorrow()->format('Y-m-d'));
  $firstRoomId = (int) (old('room_id') ?: ($type?->rooms->first()?->id ?? 0));
@endphp

<section data-anim-wrap class="pageHero -type-1 -items-center">
    <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/7.png', 'heroUrl' => $heroUrl ?? null])
    </div>
    <div class="container">
    <div class="row justify-center">
        <div class="col-auto">
            <div data-split="lines" data-anim-child="split-lines delay-3" class="pageHero__content text-center">
                <p class="pageHero__subtitle text-white uppercase mb-15">{{ __('Transparent pricing') }}</p>

                {{-- Hapa tunatumia jina la Room Type badala ya Book Here --}}
                <h1 class="pageHero__title lh-11 capitalize text-white">
                    {{ $type?->name ?? __('Book Your Stay') }}
                </h1>

                <p class="pageHero__text text-white mt-15">{{ __('Per night in Tanzanian Shillings — browse by category in a clear grid.') }}</p>
            </div>
        </div>
    </div>
</div>
</section>

<section class="layout-pt-md layout-pb-lg">
  <div class="container">
    <h1 class="text-24 fw-600 mb-25 text-center">{{ $type?->name }}</h1>
    @if ($errors->any())
      <div style="margin:0 auto 1rem;max-width:780px;padding:0.9rem 1rem;border:1px solid #fecaca;border-radius:10px;background:#fff1f2;color:#9f1239;">
        <strong style="display:block;margin-bottom:.35rem;">{{ __('Please fix these booking details:') }}</strong>
        <ul style="margin:0;padding-left:1.1rem;">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="booking-form" method="POST" action="{{ route('site.booking.store') }}">
      @csrf
      <input type="hidden" name="room_type_id" value="{{ $type?->id }}">
      <input type="hidden" name="room_id" id="selected_room_id" value="{{ $firstRoomId ?: old('room_id') }}">

      <div class="booking-grid-container">

        <!-- COLUMN 1 -->
        <div class="booking-media-col">
          <img id="booking-main-img" src="{{ $mainImage }}" alt="Room">
          <div class="d-flex gap-10 mt-15 flex-wrap">
            @foreach (array_slice($typeThumbs, 0, 4) as $thumb)
              <img src="{{ $thumb }}" onclick="document.getElementById('booking-main-img').src=this.src" style="width:75px;height:65px;object-fit:cover;border-radius:8px;cursor:pointer;border:1px solid #ddd;">
            @endforeach
          </div>
          @if($type && $type->description)
          <div class="room-description-box">
            <h4 class="text-18 fw-600 mb-10" style="color:#2563eb;">About this Room</h4>
            <p>{{ $type->description }}</p>
          </div>
          @endif
        </div>

        <!-- COLUMN 2 -->
        <div class="booking-selection-col">
          <h3 class="text-17 fw-600 mb-15">1. Select Room</h3>
          <div class="room-list-container">
            @foreach (($type?->rooms ?? collect()) as $roomOption)
              <label class="room-item-row">
                <input type="radio" name="room_pick" value="{{ $roomOption->id }}" @checked($firstRoomId === (int) $roomOption->id)>
                <div class="room-info-text">
                    {{ $roomOption->name }}
                    <div style="font-size:11px; color:#777;">No. {{ $roomOption->room_number }}{{ $roomOption->status !== 'available' ? ' • '.__('Under maintenance') : '' }}</div>
                </div>
              </label>
            @endforeach
          </div>

          <h3 class="text-17 fw-600 mb-10">2. Pick Dates</h3>
          <input type="text" id="booking_date_range" class="form-control" style="border:1px solid #d1d5db; height:46px; border-radius:8px;" placeholder="Select Dates" readonly>
          <input type="hidden" name="check_in" id="check_in" value="{{ $defaultCheckIn }}">
          <input type="hidden" name="check_out" id="check_out" value="{{ $defaultCheckOut }}">



          <div class="summary-card">
            <div class="d-flex justify-between mb-5"><span class="text-14">Price per Night:</span><strong id="booking-price-night">{{ number_format((int) round($price), 0, '.', ',') }}</strong></div>
            <div class="d-flex justify-between pt-10 border-top-light">
              <span class="text-15 fw-600">Total Amount:</span>
              <span id="booking-total" class="text-22 fw-700 text-blue-1">—</span>
            </div>
          </div>
        </div>

        <!-- COLUMN 3 -->
        <div class="booking-form-col">
          <h3 class="text-17 fw-600 mb-20">3. Guest Details</h3>
          <div class="custom-input-group"><label>First Name</label><input type="text" name="first_name" required value="{{ old('first_name') }}"></div>
          <div class="custom-input-group"><label>Last Name</label><input type="text" name="last_name" required value="{{ old('last_name') }}"></div>
          <div class="custom-input-group"><label>Email Address</label><input type="email" name="email" required value="{{ old('email', auth()->user()?->email) }}"></div>
          {{-- <div class="custom-input-group"><label>Phone Number</label><input type="text" name="phone" required value="{{ old('phone') }}"></div> --}}
          <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>


          <h3 class="text-16 fw-600 mt-25 mb-10">Payment Method</h3>
          @foreach ($methods as $m)
            <label class="payment-item">
              <input type="radio" name="booking_method_id" value="{{ $m->id }}" @checked($loop->first)>
              <span>
                <strong>{{ $m->name }}</strong>
                @if ($m->account_number || $m->account_holder)
                  <span style="display:block;font-size:12px;opacity:.75;">
                    {{ trim(($m->account_number ? __('Account').': '.$m->account_number : '').($m->account_holder ? ' · '.__('Holder').': '.$m->account_holder : '')) }}
                  </span>
                @endif
                @if ($m->instructions)
                  <span style="display:block;font-size:12px;opacity:.75;">{{ $m->instructions }}</span>
                @endif
              </span>
            </label>
          @endforeach

          <label class="d-flex items-center mt-20 cursor-pointer">
            <input type="checkbox" name="terms" required class="mr-10" style="width:17px; height:17px;">
            <span class="text-13">I agree to the terms & conditions</span>
          </label>
          <button type="submit" class="button -md bg-accent-1 -accent-2 text-white w-1/1 mt-25 fw-600">COMPLETE BOOKING</button>
        </div>
      </div>
    </form>
  </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script><script>
(function () {
    var roomPrices = @json($roomPrices ?? []);
    var defaultNightPrice = {{ (int) round($price) }};
    var priceNightLabel = document.getElementById('booking-price-night');

    function nightPriceForRoom(roomId) {
        if (!roomId) return defaultNightPrice;
        var p = roomPrices[String(roomId)] ?? roomPrices[roomId];
        if (typeof p === 'number') return p;
        return defaultNightPrice;
    }

    var selectedRoomEl = document.getElementById('selected_room_id');
    var pricePerNight = nightPriceForRoom(selectedRoomEl ? selectedRoomEl.value : '');
    var ci = document.getElementById('check_in');
    var co = document.getElementById('check_out');
    var totalEl = document.getElementById('booking-total');

    // Hizi ni tarehe za kuanzia (Initial disabled dates)
    var bookedRanges = @json($disabledDates ?? []);

    // Function ya kurekebisha tarehe kuwa string YYYY-MM-DD
    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    // Hesabu ya Bei (Namba Nzima)
    function recalc() {
        if (!ci.value || !co.value) return;

        var dateIn = new Date(ci.value);
        var dateOut = new Date(co.value);
        dateIn.setHours(12, 0, 0, 0);
        dateOut.setHours(12, 0, 0, 0);

        var diffTime = dateOut.getTime() - dateIn.getTime();
        var nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (nights <= 0) nights = 1;
        var totalAmount = pricePerNight * nights;

        if (totalEl) {
            totalEl.textContent = Math.round(totalAmount).toLocaleString(undefined, { maximumFractionDigits: 0 });
        }
    }

    // Kalenda Initialization
    var fp = flatpickr("#booking_date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        minDate: "today",
        defaultDate: (ci.value && co.value) ? [ci.value, co.value] : undefined,
        // Hapa tunafunga tarehe ambazo zimekaliwa au zina marekebisho
        disable: bookedRanges,
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            var dStrLocal = formatDate(dayElem.dateObj);

            // Angalia kama tarehe hii ipo kwenye list ya zilizofungwa
            var isBlocked = bookedRanges.some(function(r) {
                return dStrLocal >= r.from && dStrLocal <= r.to;
            });

            if (isBlocked) {
                dayElem.classList.add('booking-day-booked');
                dayElem.title = "Not Available";
            } else if (dayElem.dateObj >= new Date().setHours(0,0,0,0)) {
                dayElem.classList.add('booking-day-available');
            }
        },
        onChange: function(dates) {
            if (dates.length === 2) {
                ci.value = formatDate(dates[0]);
                co.value = formatDate(dates[1]);
                recalc();
            }
        }
    });

    // Function ya kupata tarehe za chumba husika (AJAX)
async function loadRoomCalendar(roomId) {
    if (!roomId) return;

    try {
        let res = await fetch(`{{ route('site.booking.room-calendar') }}?room_id=${roomId}`);
        let data = await res.json();

        // Tunachukua tarehe tu na kuziweka kwenye kalenda
        bookedRanges = data.booked_ranges || [];

        // Sasisha Flatpickr
        fp.set("disable", bookedRanges);
        fp.redraw();

        // MISTARI YA ALERT IMEFUTWA HAPA...
    } catch (e) {
        console.error("Calendar Error:", e);
    }
}

    // Sikiliza mabadiliko ya chumba (Kama kuna option ya kuchagua chumba kingine)
    document.querySelectorAll('input[name="room_pick"]').forEach(input => {
        input.onchange = () => {
            var selectedId = input.value;
            var hiddenInput = document.getElementById('selected_room_id');
            if (hiddenInput) hiddenInput.value = selectedId;
            pricePerNight = nightPriceForRoom(selectedId);
            if (priceNightLabel) {
                priceNightLabel.textContent = pricePerNight.toLocaleString(undefined, { maximumFractionDigits: 0 });
            }
            loadRoomCalendar(selectedId);
            recalc();
        };
    });

    var initialRoom = @json($firstRoomId ? (string) $firstRoomId : request()->query('room_id', ''));
    if (initialRoom) {
        loadRoomCalendar(initialRoom);
    }

    recalc();
})();
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.querySelector("#phone");

    const iti = window.intlTelInput(input, {
        initialCountry: "tz",
        separateDialCode: true,
        preferredCountries: ["tz", "ke", "ug"],
        nationalMode: false, // muhimu sana
        autoPlaceholder: "polite",
        formatOnDisplay: true,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
    });

    // Remove leading zero automatically
    input.addEventListener("input", function () {
        let value = input.value;

        // kama user ameanza na 0 → iondolewe
        if (value.startsWith("0")) {
            input.value = value.substring(1);
        }
    });

    // Save full international format
    input.form.addEventListener("submit", function () {
        if (input.value.trim() !== "") {
            input.value = iti.getNumber(); // +255621234567
        }
    });
});
</script>
@endpush
@endsection
