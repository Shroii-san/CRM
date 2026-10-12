<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\InteractionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteractionController extends Controller
{
    public function __construct(private InteractionService $interactionService)
    {
    }

    /**
     * Tampilkan halaman utama Interaction (Blade View).
     */
    public function index(Request $request): View
    {
        $interactions = $this->interactionService->list(user: $request->user());

        return view('pages.interaction', compact('interactions'));
    }
}
