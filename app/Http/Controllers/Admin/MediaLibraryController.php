<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaLibraryController extends Controller
{
    public function index(): View
    {
        return view('admin.media-library.index', [
            'assets' => MediaAsset::query()->latest()->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['required', 'file', 'max:51200', 'extensions:jpg,jpeg,png,gif,webp,avif,mp4,webm,mov'],
        ]);

        foreach ($data['files'] as $file) {
            MediaAsset::createFromUpload($file);
        }

        return back()->with('status', __('Media uploaded to library.'));
    }
}
