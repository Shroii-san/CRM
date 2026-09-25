<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SalesVisitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesVisitController extends Controller
{
    public function __construct(private SalesVisitService $visitService)
    {
    }

    /**
     * Tampilkan halaman utama Sales Visit.
     */
    public function index(Request $request): View
    {
        $visits = $this->visitService->list(user: $request->user());

        return view('pages.salesvisit', compact('visits'));
    }
}
