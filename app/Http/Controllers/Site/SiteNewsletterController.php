<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteNewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:filter', 'max:255'],
        ]);

        NewsletterSubscription::query()->firstOrCreate(
            ['email' => mb_strtolower($validated['email'])],
        );

        return back()->with('newsletter_ok', __('Thank you — you are subscribed to our updates.'));
    }
}
