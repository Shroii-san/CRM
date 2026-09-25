<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\NoteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function __construct(private NoteService $noteService)
    {
    }

    /**
     * Tampilkan halaman utama Note Management (Blade View).
     */
    public function index(Request $request): View
    {
        $notes = $this->noteService->list(user: $request->user());

        return view('pages.note', compact('notes'));
    }
}
