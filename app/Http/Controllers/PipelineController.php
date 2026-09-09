<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesVisit;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PipelineController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // 1️⃣ LEAD - Semua kunjungan pertama (initial visit)
        $leadQuery = SalesVisit::with(['sales', 'company', 'province', 'regency', 'pic'])
            ->where('is_follow_up', false);
        
        // Role-based filtering untuk Lead
        if ($user->role_id == 1) {
            // superadmin - lihat semua
        } elseif (in_array($user->role_id, [7, 11])) {
            $leadQuery->whereHas('sales', function ($q) {
                $q->where('role_id', 12);
            });
        } elseif ($user->role_id == 12) {
            $leadQuery->where('sales_id', $user->user_id);
        } else {
            $leadQuery->whereNull('id');
        }
        
        $leads = $leadQuery->orderBy('visit_date', 'desc')->get();
        
        // 2️⃣ VISIT - Semua kunjungan (visit) tanpa filter
        $visitQuery = SalesVisit::with(['sales', 'company', 'province', 'regency', 'pic']);
        
        if ($user->role_id == 1) {
        } elseif (in_array($user->role_id, [7, 11])) {
            $visitQuery->whereHas('sales', function ($q) {
                $q->where('role_id', 12);
            });
        } elseif ($user->role_id == 12) {
            $visitQuery->where('sales_id', $user->user_id);
        } else {
            $visitQuery->whereNull('id');
        }
        
        $visits = $visitQuery->orderBy('visit_date', 'desc')->get();
        
        // 3️⃣ FOLLOW UP - Semua kunjungan follow up
        $followUpQuery = SalesVisit::with(['sales', 'company', 'province', 'regency', 'pic'])
            ->where('is_follow_up', true);
        
        if ($user->role_id == 1) {
        } elseif (in_array($user->role_id, [7, 11])) {
            $followUpQuery->whereHas('sales', function ($q) {
                $q->where('role_id', 12);
            });
        } elseif ($user->role_id == 12) {
            $followUpQuery->where('sales_id', $user->user_id);
        } else {
            $followUpQuery->whereNull('id');
        }
        
        $followUps = $followUpQuery->orderBy('visit_date', 'desc')->get();
        
        // 4️⃣ TRANSAKSI - dari Transaksi
        $transaksiQuery = Transaksi::with(['sales', 'company', 'pic']);
        
        if ($user->role_id == 1) {
        } elseif (in_array($user->role_id, [7, 11])) {
            $transaksiQuery->whereHas('sales', function ($q) {
                $q->where('role_id', 12);
            });
        } elseif ($user->role_id == 12) {
            $transaksiQuery->where('sales_id', $user->user_id);
        } else {
            $transaksiQuery->whereNull('id');
        }
        
        $transaksi = $transaksiQuery->orderBy('created_at', 'desc')->get();
        
        $currentMenuId = 17;
        
        return view('pages.pipeline', compact('leads', 'visits', 'followUps', 'transaksi', 'currentMenuId'));
    }

    public function showLead($id)
    {
        try {
            $lead = SalesVisit::with(['sales', 'company', 'province', 'regency', 'district', 'village', 'pic'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'type' => 'lead',
                'data' => [
                    'id' => $lead->id,
                    'company' => $lead->company->company_name ?? '-',
                    'pic_name' => $lead->pic_name ?? '-',
                    'pic_phone' => optional($lead->pic)->phone ?? '-',
                    'pic_email' => optional($lead->pic)->email ?? '-',
                    'pic_position' => optional($lead->pic)->position ?? '-',
                    'sales_name' => optional($lead->sales)->username ?? '-',
                    'sales_email' => optional($lead->sales)->email ?? '-',
                    'visit_date' => $lead->visit_date->format('d/m/Y'),
                    'location' => collect([
                        optional($lead->province)->name,
                        optional($lead->regency)->name,
                        optional($lead->district)->name,
                        optional($lead->village)->name
                    ])->filter()->implode(', ') ?: '-',
                    'address' => $lead->address ?? '-',
                    'visit_purpose' => $lead->visit_purpose ?? '-',
                    'created_at' => $lead->created_at->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function showVisit($id)
    {
        try {
            $visit = SalesVisit::with(['sales', 'company', 'province', 'regency', 'district', 'village', 'pic'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'type' => 'visit',
                'data' => [
                    'id' => $visit->id,
                    'company' => optional($visit->company)->company_name ?? '-',
                    'pic_name' => $visit->pic_name ?? '-',
                    'pic_phone' => optional($visit->pic)->phone ?? '-',
                    'pic_email' => optional($visit->pic)->email ?? '-',
                    'pic_position' => optional($visit->pic)->position ?? '-',
                    'sales_name' => optional($visit->sales)->username ?? '-',
                    'sales_email' => optional($visit->sales)->email ?? '-',
                    'visit_date' => $visit->visit_date->format('d/m/Y'),
                    'location' => collect([
                        optional($visit->province)->name,
                        optional($visit->regency)->name,
                        optional($visit->district)->name,
                        optional($visit->village)->name
                    ])->filter()->implode(', ') ?: '-',
                    'address' => $visit->address ?? '-',
                    'visit_purpose' => $visit->visit_purpose ?? '-',
                    'is_follow_up' => $visit->is_follow_up ? 'Ya' : 'Tidak',
                    'created_at' => $visit->created_at->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function showFollowUp($id)
    {
        return $this->showVisit($id);
    }

    public function showTransaksi($id)
    {
        try {
            $transaksi = Transaksi::with(['sales', 'company', 'pic', 'salesVisit'])
                ->findOrFail($id);
            
            $workDuration = $this->calculateWorkDuration(
                $transaksi->tanggal_mulai_kerja,
                $transaksi->tanggal_selesai_kerja
            );
            
            return response()->json([
                'success' => true,
                'type' => 'transaksi',
                'data' => [
                    'id' => $transaksi->id,
                    'nama_perusahaan' => $transaksi->nama_perusahaan,
                    'pic_name' => $transaksi->pic_name ?? '-',
                    'pic_phone' => optional($transaksi->pic)->phone ?? '-',
                    'pic_email' => optional($transaksi->pic)->email ?? '-',
                    'nama_sales' => $transaksi->nama_sales,
                    'sales_email' => optional($transaksi->sales)->email ?? '-',
                    'nilai_proyek' => 'Rp ' . number_format($transaksi->nilai_proyek, 0, ',', '.'),
                    'status' => $transaksi->status,
                    'tanggal_mulai_kerja' => $transaksi->tanggal_mulai_kerja ? Carbon::parse($transaksi->tanggal_mulai_kerja)->format('d/m/Y') : '-',
                    'tanggal_selesai_kerja' => $transaksi->tanggal_selesai_kerja ? Carbon::parse($transaksi->tanggal_selesai_kerja)->format('d/m/Y') : '-',
                    'work_duration' => $workDuration,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'created_at' => $transaksi->created_at->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    private function calculateWorkDuration($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return '-';
        }

        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            $days = $start->diffInDays($end);
            $hours = $start->diffInHours($end) % 24;
            
            if ($days > 0 && $hours > 0) {
                return "$days hari $hours jam";
            } elseif ($days > 0) {
                return "$days hari";
            } elseif ($hours > 0) {
                return "$hours jam";
            } else {
                return "0 jam";
            }
        } catch (\Exception $e) {
            return '-';
        }
    }
}