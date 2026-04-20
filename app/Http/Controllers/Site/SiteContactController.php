<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class SiteContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect()
            ->route('site.page', ['slug' => 'contact'])
            ->with('status', __('Thank you — we will get back to you soon.'));
    }
}
