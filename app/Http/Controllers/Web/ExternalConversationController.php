<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ExternalConversationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExternalConversationController extends Controller
{
    public function __construct(private ExternalConversationService $conversationService)
    {
    }

    /**
     * Tampilkan halaman utama External Conversation (Blade View).
     */
    public function index(Request $request): View
    {
        $conversations = $this->conversationService->list();

        return view('pages.external_conversation', compact('conversations'));
    }
}
