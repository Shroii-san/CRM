<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DealService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealController extends Controller
{
    public function __construct(private DealService $dealService)
    {
    }

    /**
     * Tampilkan halaman utama Transaksi / Deals.
     */
    public function index(Request $request): View
    {
        $deals = $this->dealService->list(user: $request->user());

        return view('pages.transaksi', compact('deals'));
    }
}
