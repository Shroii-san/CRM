<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\PipelineStage;

class DashboardController extends Controller
{
    public function index()
    {
        $revenue = Deal::where('status', 2)->sum('value');

        $stats = [
            'total_clients' => Client::count(),
            'total_open_deals' => Deal::where('status', 1)->count(),
            'total_won_deals' => Deal::where('status', 2)->count(),
            'total_revenue' => number_format($revenue, 0, ',', '.'),
        ];

        return view('pages.dashboard', compact('stats'));
    }
}
