<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $q = trim((string) request('q', ''));
        $messages = ContactMessage::query()
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('body', 'like', '%'.$q.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.contacts.index', [
            'messages' => $messages,
            'q' => $q,
        ]);
    }

    public function reply(Request $request, ContactMessage $message): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Mail::raw($data['body'], function ($mail) use ($message, $data): void {
            $mail->to($message->email)
                ->subject($data['subject']);
        });

        return back()->with('status', __('Reply sent to :email', ['email' => $message->email]));
    }
    public function destroy(ContactMessage $message): \Illuminate\Http\RedirectResponse
{
    $message->delete();

    return back()->with('status', __('Message deleted successfully.'));
}
}
