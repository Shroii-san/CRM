<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Task;
use App\Models\Interaction;

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

        $followUps = Task::query()
            ->with('deal.client')
            ->where('status', 1)
            ->whereNotNull('due_at')
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        $urgentCount = Task::query()
            ->where('status', 1)
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->addDay())
            ->count();

        $communications = Interaction::query()
            ->whereDate('created_at', today())
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->get();

        return view('pages.dashboard', compact('stats', 'followUps', 'urgentCount', 'communications'));
    }
}
