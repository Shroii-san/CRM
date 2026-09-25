<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $logService)
    {
    }

    /**
     * Tampilkan halaman audit activity log (Blade View).
     */
    public function index(Request $request): View
    {
        $logs = $this->logService->list(user: $request->user());

        return view('pages.activity_log', compact('logs'));
    }
}
