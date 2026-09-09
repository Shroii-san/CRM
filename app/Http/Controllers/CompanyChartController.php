<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Support\Facades\DB;

class CompanyChartController extends Controller
{
    public function index()
    {
        $companies = Company::with('companyType')->get();
        $companyTypes = CompanyType::all();

        // Convert Collections to arrays untuk JavaScript
        $tierGrouped = $companies->groupBy('tier');
        $statusGrouped = $companies->groupBy('status');

        // Build chart data dengan explicit array conversion
        $chartCompanyData = [
            'type' => [
                'labels' => $companyTypes->pluck('type_name')->toArray(),
                'data' => $companyTypes->map(function($type) use ($companies) {
                    return $companies->where('company_type_id', $type->company_type_id)->count();
                })->values()->toArray(), // tambah values() untuk re-index
                'details' => $companyTypes->mapWithKeys(function($type) use ($companies) {
                    return [
                        $type->type_name => $companies->where('company_type_id', $type->company_type_id)
                            ->pluck('company_name')->toArray()
                    ];
                })->toArray()
            ],
            'tier' => [
                'labels' => $tierGrouped->keys()->toArray(),
                'data' => $tierGrouped->map(function($group) {
                    return $group->count();
                })->values()->toArray(),
                'details' => $tierGrouped->map(function($group) {
                    return $group->pluck('company_name')->toArray();
                })->toArray()
            ],
            'status' => [
                'labels' => $statusGrouped->keys()->toArray(),
                'data' => $statusGrouped->map(function($group) {
                    return $group->count();
                })->values()->toArray(),
                'details' => $statusGrouped->map(function($group) {
                    return $group->pluck('company_name')->toArray();
                })->toArray()
            ]
        ];

        // Legacy chartData untuk backward compatibility
        $chartData = [
            'tier' => $tierGrouped->map(function($group) {
                return $group->count();
            }),
            'status' => $statusGrouped->map(function($group) {
                return $group->count();
            }),
        ];

        return view('pages.dashboard', compact('companies', 'companyTypes', 'chartData', 'chartCompanyData'));
    }

    // ==========================
    // BAR CHART - Geographic Distribution by Tier
    // ==========================

    /**
     * Get geographic distribution data by tier untuk Bar Chart
     */
    public function getGeoDistributionBar()
    {
        try {
            // Query untuk mendapatkan distribusi customer berdasarkan tier
            $distribution = DB::table('company')
                ->select('tier', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tier')
                ->groupBy('tier')
                ->orderByRaw("array_position(ARRAY['A','B','C','D'], tier)")
                ->get();

            // Hitung total customer
            $totalCustomers = $distribution->sum('total');
            
            // Hitung jumlah tier aktif
            $activeTiers = $distribution->count();
            
            // Dapatkan tier dengan customer tertinggi
            $highestTier = $distribution->first();

            // Format data untuk bar chart
            $chartData = [
                'labels' => $distribution->pluck('tier')->toArray(),
                'data' => $distribution->pluck('total')->toArray(),
                'colors' => $this->generateColors($distribution->count())
            ];

            // Summary statistics
            $stats = [
                'total_customers' => $totalCustomers,
                'active_tiers' => $activeTiers,
                'highest_count' => $highestTier ? $highestTier->total : 0,
                'highest_tier' => $highestTier ? $highestTier->tier : '-'
            ];

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'stats' => $stats,
                'details' => $distribution
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching geographic distribution: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed tier information untuk Bar Chart
     */
    public function getTierDetailBar($tier)
    {
        try {
            $customers = DB::table('company')
                ->where('tier', $tier)
                ->select('company_id as id', 'company_name as name', 'tier', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'tier' => $tier,
                'count' => $customers->count(),
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tier detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export geo distribution data dari Bar Chart
     */
    public function exportGeoDataBar()
    {
        try {
            $distribution = DB::table('company')
                ->select('tier', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tier')
                ->groupBy('tier')
                ->orderByRaw("array_position(ARRAY['A','B','C','D'], tier)")
                ->get();

            $csvData = "Tier,Total Customer,Percentage\n";
            $total = $distribution->sum('total');

            foreach ($distribution as $item) {
                $percentage = $total > 0 ? round(($item->total / $total) * 100, 2) : 0;
                $csvData .= "{$item->tier},{$item->total},{$percentage}%\n";
            }

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="geo_distribution_bar_' . date('Y-m-d') . '.csv"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================
    // PIE CHART - Geographic Distribution by Tier
    // ==========================

    /**
     * Get geographic distribution data by tier untuk Pie Chart
     */
    public function getGeoDistributionPie()
    {
        try {
            // Query untuk mendapatkan distribusi customer berdasarkan tier
            $distribution = DB::table('company')
                ->select('tier', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tier')
                ->groupBy('tier')
                ->orderByRaw("array_position(ARRAY['A','B','C','D'], tier)")
                ->get();

            // Hitung total customer
            $totalCustomers = $distribution->sum('total');
            
            // Format data untuk pie chart dengan percentage
            $chartData = [
                'labels' => $distribution->pluck('tier')->toArray(),
                'data' => $distribution->pluck('total')->toArray(),
                'percentages' => $distribution->map(function($item) use ($totalCustomers) {
                    return $totalCustomers > 0 ? round(($item->total / $totalCustomers) * 100, 1) : 0;
                })->toArray(),
                'colors' => $this->generateColors($distribution->count())
            ];

            // Summary statistics
            $stats = [
                'total_customers' => $totalCustomers,
                'total_tiers' => $distribution->count(),
                'largest_segment' => $distribution->first() ? $distribution->first()->tier : '-',
                'largest_percentage' => $distribution->first() && $totalCustomers > 0 
                    ? round(($distribution->first()->total / $totalCustomers) * 100, 1) 
                    : 0
            ];

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'stats' => $stats,
                'details' => $distribution
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pie chart data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed tier information untuk Pie Chart
     */
    public function getTierDetailPie($tier)
    {
        try {
            $customers = DB::table('company')
                ->where('tier', $tier)
                ->select('company_id as id', 'company_name as name', 'tier', 'status', 'created_at')
                ->get();

            $total = DB::table('company')
                ->whereNotNull('tier')
                ->count();

            $percentage = $total > 0 ? round(($customers->count() / $total) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'tier' => $tier,
                'count' => $customers->count(),
                'percentage' => $percentage,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tier detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export geo distribution data dari Pie Chart
     */
    public function exportGeoDataPie()
    {
        try {
            $distribution = DB::table('company')
                ->select('tier', DB::raw('COUNT(*) as total'))
                ->whereNotNull('tier')
                ->groupBy('tier')
                ->orderByRaw("array_position(ARRAY['A','B','C','D'], tier)")
                ->get();

            $csvData = "Tier,Total Customer,Percentage,Status\n";
            $total = $distribution->sum('total');

            foreach ($distribution as $item) {
                $percentage = $total > 0 ? round(($item->total / $total) * 100, 2) : 0;
                $status = $percentage >= 20 ? 'Major' : ($percentage >= 10 ? 'Medium' : 'Minor');
                $csvData .= "{$item->tier},{$item->total},{$percentage}%,{$status}\n";
            }

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="geo_distribution_pie_' . date('Y-m-d') . '.csv"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================
    // SHARED HELPER METHODS
    // ==========================

    /**
     * Generate colors for charts
     */
    private function generateColors($count)
    {
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(249, 115, 22, 0.8)',   // Orange
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(14, 165, 233, 0.8)',   // Sky
            'rgba(245, 158, 11, 0.8)',   // Amber
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(34, 197, 94, 0.8)',    // Emerald
            'rgba(168, 85, 247, 0.8)',   // Violet
        ];

        return array_slice($colors, 0, $count);
    }
}