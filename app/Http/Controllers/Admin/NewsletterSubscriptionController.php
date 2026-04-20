<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriptionController extends Controller
{
    public function index(): View
    {
        $subscriptions = NewsletterSubscription::query()->latest()->paginate(25);

        return view('admin.emails.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function destroy(NewsletterSubscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return back()->with('status', __('Subscriber removed.'));
    }
}
