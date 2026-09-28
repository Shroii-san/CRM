<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
    }

    /**
     * Tampilkan halaman utama Attachment Management (Blade View).
     */
    public function index(Request $request): View
    {
        $attachments = $this->attachmentService->list();

        return view('pages.attachment', compact('attachments'));
    }
}
