<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReceptionContactController extends Controller
{
    use InteractsWithStaffScope;

    public function index(): View
    {
        $q = trim((string) request('q', ''));
        $messages = ContactMessage::query()->with('branch');
        $this->scope()->filterContactMessagesByBranch($messages);

        $messages = $messages
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

        return view('reception.contacts.index', [
            'messages' => $messages,
            'q' => $q,
        ]);
    }

    public function reply(Request $request, ContactMessage $message): RedirectResponse
    {
        $this->ensureMessageInScope($message);

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

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $this->ensureMessageInScope($message);
        $message->delete();

        return back()->with('status', __('Message deleted.'));
    }

    private function ensureMessageInScope(ContactMessage $message): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }

        abort_unless(
            $message->hotel_branch_id !== null && in_array((int) $message->hotel_branch_id, $ids, true),
            403
        );
    }
}
