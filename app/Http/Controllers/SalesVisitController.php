<?php

namespace App\Http\Controllers;

use App\Models\SalesVisit;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SalesVisitController extends Controller
{

public function index(Request $request)
{
    $user = Auth::user();

    // Base query dengan relasi
    $visitsQuery = SalesVisit::with(['sales', 'company', 'province', 'regency', 'district', 'village']);

    // Role-based filtering
    if ($user->role_id == 1) {
        // superadmin
    } elseif (in_array($user->role_id, [7, 11])) {
        $visitsQuery->whereHas('sales', function ($q) {
            $q->where('role_id', 12);
        });
    } elseif ($user->role_id == 12) {
        $visitsQuery->where('sales_id', $user->user_id);
    } else {
        $visitsQuery->whereNull('id');
    }

    $salesVisits = $visitsQuery->orderBy('visit_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Sales Users
    $salesUsers = User::where('role_id', 12)
        ->select('user_id', 'username', 'email')
        ->orderBy('username')
        ->get()
        ->map(function($user) {
            return (object)[
                'id' => $user->user_id,
                'name' => $user->username . ' - ' . $user->email,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email
            ];
        });

    // Provinces
    $provinces = Province::select('id', 'name')
        ->orderBy('name')
        ->get();

    // Company Types
    $types = CompanyType::where('is_active', true)->get();

    // KPI
    $totalVisits = (clone $visitsQuery)->count();
    $followUpVisits = (clone $visitsQuery)->where('is_follow_up', true)->count();
    $uniquePics = (clone $visitsQuery)->distinct('pic_name')->count('pic_name');
    $uniqueSales = (clone $visitsQuery)->distinct('sales_id')->count('sales_id');

    return view('pages.salesvisit', compact(
        'salesVisits',
        'salesUsers',
        'provinces',
        'types',
        'totalVisits',
        'followUpVisits',
        'uniquePics',
        'uniqueSales'
    ));
}

// 🔥 NEW: Show Visit Detail with PIC
public function show($id)
{
    try {
        $visit = SalesVisit::with([
            'company', 
            'sales', 
            'pic',
            'province', 
            'regency', 
            'district', 
            'village'
        ])->findOrFail($id);
        
        // Build location string
        $locationParts = [];
        if ($visit->province) $locationParts[] = $visit->province->name;
        if ($visit->regency) $locationParts[] = $visit->regency->name;
        if ($visit->district) $locationParts[] = $visit->district->name;
        if ($visit->village) $locationParts[] = $visit->village->name;
        
        return response()->json([
            'success' => true,
            'visit' => [
                'visit_date' => $visit->visit_date->format('d/m/Y'),
                'sales_name' => $visit->sales->username ?? '-',
                'company_name' => $visit->company->company_name ?? '-',
                'location' => implode(', ', $locationParts) ?: '-',
                'address' => $visit->address ?? '-',
                'visit_purpose' => $visit->visit_purpose ?? '-',
                'is_follow_up' => $visit->is_follow_up
            ],
            'pic' => $visit->pic ? [
                'pic_name' => $visit->pic->pic_name,
                'position' => $visit->pic->position ?? '-',
                'phone' => $visit->pic->phone ?? '-',
                'email' => $visit->pic->email ?? '-'
            ] : null
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching visit detail: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data'
        ], 500);
    }
}

public function getSalesUsers()
{
    $salesUsers = User::where('role_id', 12)
        ->select('user_id', 'username', 'email')
        ->orderBy('username')
        ->get();
    
    return response()->json([
        'users' => $salesUsers
    ]);
}

public function search(Request $request)
{
    try {
        \Log::info('🔍 SalesVisit Search Request', [
            'all_params' => $request->all(),
            'query' => $request->q,
            'sales' => $request->sales,
            'province' => $request->province
        ]);

        $user = Auth::user();

        // Base query dengan eager loading
        $query = SalesVisit::with(['sales', 'company', 'province', 'regency', 'district', 'village']);

        // Role-based filtering
        if ($user->role_id == 1) {
            \Log::info('✅ Superadmin access - all data');
        } elseif (in_array($user->role_id, [7, 11])) {
            $query->whereHas('sales', function ($q) {
                $q->where('role_id', 12);
            });
            \Log::info('✅ Manager access - sales only');
        } elseif ($user->role_id == 12) {
            $query->where('sales_id', $user->user_id);
            \Log::info('✅ Sales access - own data only', ['user_id' => $user->user_id]);
        } else {
            $query->whereNull('id');
            \Log::info('⛔ No access');
        }

        // Filter by Sales
        if ($request->filled('sales') && $request->sales !== '' && $request->sales !== 'all') {
            $salesId = $request->sales;
            $query->where('sales_id', $salesId);
            \Log::info('✅ Sales filter applied', ['sales_id' => $salesId]);
        }

        // Filter by Province
        if ($request->filled('province') && $request->province !== '' && $request->province !== 'all') {
            $provinceId = $request->province;
            $query->where('province_id', $provinceId);
            \Log::info('✅ Province filter applied', ['province_id' => $provinceId]);
        }

        // SEARCH - Include company name
        if ($request->filled('q') && trim($request->q) !== '') {
            $search = trim($request->q);
            $searchPattern = "%{$search}%";
            
            $query->where(function($q) use ($searchPattern) {
                $q->where('pic_name', 'ILIKE', $searchPattern)
                  ->orWhere('address', 'ILIKE', $searchPattern)
                  ->orWhere('visit_purpose', 'ILIKE', $searchPattern)
                  
                  // Search company name
                  ->orWhereHas('company', function($cq) use ($searchPattern) {
                      $cq->where('company_name', 'ILIKE', $searchPattern);
                  })
                  
                  // Search sales name
                  ->orWhereHas('sales', function($sq) use ($searchPattern) {
                      $sq->where('username', 'ILIKE', $searchPattern)
                         ->orWhere('email', 'ILIKE', $searchPattern);
                  })
                  
                  // Search province
                  ->orWhereHas('province', function($pq) use ($searchPattern) {
                      $pq->where('name', 'ILIKE', $searchPattern);
                  })
                  
                  // Search regency
                  ->orWhereHas('regency', function($rq) use ($searchPattern) {
                      $rq->where('name', 'ILIKE', $searchPattern);
                  });
            });
            
            \Log::info('✅ Search applied', ['keyword' => $search]);
        }

        // Sort
        $query->orderBy('visit_date', 'desc')->orderBy('created_at', 'desc');

        // Paginate
        $salesVisits = $query->paginate(10);

        \Log::info('📊 Results', [
            'count' => $salesVisits->count(), 
            'total' => $salesVisits->total()
        ]);

        // Format response
        $items = $salesVisits->map(function($visit, $index) use ($salesVisits) {
        // ✅ BUILD LOCATION dengan format 2 baris
        $province = optional($visit->province)->name ?? '';
        $regency = optional($visit->regency)->name ?? '';
        $district = optional($visit->district)->name ?? '';
        $village = optional($visit->village)->name ?? '';
        
        // Format: Province (main), kemudian detail di bawahnya
        $locationMain = $province ?: '-';
        $locationSub = collect([$regency, $district, $village])
            ->filter()
            ->implode(', ');
        
        // Gabung jadi satu string dengan separator '|'
        $locationDisplay = $locationMain;
        if ($locationSub) {
            $locationDisplay .= ' | ' . $locationSub;
        }

        $actions = [];
        try {
            $actions = $this->getVisitActions($visit);
        } catch (\Exception $e) {
            \Log::error('Error generating actions', [
                'visit_id' => $visit->id,
                'error' => $e->getMessage()
            ]);
        }

        return [
            'number' => $salesVisits->firstItem() + $index,
            'visit_date' => $visit->visit_date ? $visit->visit_date->format('d M Y') : '-',
            'company' => optional($visit->company)->company_name ?? '-',
            'pic' => $visit->pic_name ?? '-',
            'location' => $locationDisplay,
            'purpose' => $visit->visit_purpose ? \Illuminate\Support\Str::limit($visit->visit_purpose, 45) : '-',
            'sales' => [
                'username' => optional($visit->sales)->username ?? '-',
                'email' => optional($visit->sales)->email ?? 'No email'
            ],
            'follow_up' => $visit->is_follow_up ? 'Ya' : 'Tidak',
            'actions' => $actions
        ];
    })->toArray();

        return response()->json([
            'success' => true,
            'items' => $items,
            'pagination' => [
                'current_page' => $salesVisits->currentPage(),
                'last_page' => $salesVisits->lastPage(),
                'from' => $salesVisits->firstItem(),
                'to' => $salesVisits->lastItem(),
                'total' => $salesVisits->total(),
                'per_page' => $salesVisits->perPage()
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ SalesVisit Search Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function store(Request $request)
{
    \Log::info('Store Request Data:', $request->all());
    
    $request->validate([
        'sales_id' => 'required|exists:users,user_id',
        'pic_name' => 'required|string|max:255',
        'pic_id' => 'nullable|exists:company_pics,pic_id',
        'company_name' => 'nullable|string|max:255',
        'company_id' => 'nullable|exists:company,company_id',
        'province_id' => 'required|exists:provinces,id',
        'regency_id' => 'nullable|exists:regencies,id',
        'district_id' => 'nullable|exists:districts,id',
        'village_id' => 'nullable|exists:villages,id',
        'address' => 'nullable|string',
        'visit_date' => 'required|date',
        'visit_purpose' => 'required|string',
        'is_follow_up' => 'nullable|boolean',
        // Tambahan field untuk PIC baru
        'pic_position' => 'nullable|string|max:255',
        'pic_phone' => 'nullable|string|max:20',
        'pic_email' => 'nullable|email|max:255',
    ]);

    $user = Auth::user();

    // Check permission
    $allowedRoles = ['superadmin', 'admin', 'marketing', 'sales'];
    $userRoleName = strtolower($user->role->role_name ?? '');
    
    if (!in_array($userRoleName, $allowedRoles)) {
        return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menambah kunjungan.');
    }

    // Untuk sales, pastikan sales_id adalah user_id mereka sendiri
    if ($userRoleName === 'sales') {
        $request->merge(['sales_id' => $user->user_id]);
    }

    try {
        DB::beginTransaction();

        $picId = $request->pic_id;

        // Jika ada company_id tapi tidak ada pic_id, buat PIC baru
        if ($request->company_id && !$request->pic_id) {
            $companyPic = \App\Models\CompanyPic::create([
                'company_id' => $request->company_id,
                'pic_name' => $request->pic_name,
                'position' => $request->pic_position,
                'phone' => $request->pic_phone,
                'email' => $request->pic_email,
            ]);
            
            $picId = $companyPic->pic_id;
            \Log::info('✅ PIC baru dibuat:', $companyPic->toArray());
        }

        $salesVisit = SalesVisit::create([
            'sales_id' => $request->sales_id,
            'user_id' => $user->user_id,
            'pic_name' => $request->pic_name,
            'pic_id' => $picId,
            'company_name' => $request->company_name ?? null,
            'company_id' => $request->company_id ?? null,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id ?? null,
            'district_id' => $request->district_id ?? null,
            'village_id' => $request->village_id ?? null,
            'address' => $request->address ?? null,
            'visit_date' => $request->visit_date,
            'visit_purpose' => $request->visit_purpose,
            'is_follow_up' => $request->boolean('is_follow_up') ?? false,
        ]);

        DB::commit();

        \Log::info('Sales Visit Created Successfully:', $salesVisit->toArray());

        return redirect()->route('salesvisit')
            ->with('success', 'Data kunjungan sales berhasil ditambahkan!');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error storing sales visit: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
            ->withInput();
    }
}

public function update(Request $request, $id)
{
    $request->validate([
        'sales_id' => 'required|exists:users,user_id',
        'pic_name' => 'required|string|max:255',
        'pic_id' => 'nullable|exists:company_pics,pic_id',
        'company_name' => 'nullable|string|max:255',
        'company_id' => 'nullable|exists:company,company_id',
        'province_id' => 'required|exists:provinces,id',
        'regency_id' => 'nullable|exists:regencies,id',
        'district_id' => 'nullable|exists:districts,id',
        'village_id' => 'nullable|exists:villages,id',
        'address' => 'nullable|string',
        'visit_date' => 'required|date',
        'visit_purpose' => 'required|string',
        'is_follow_up' => 'nullable|boolean',
        // Tambahan field untuk PIC baru
        'pic_position' => 'nullable|string|max:255',
        'pic_phone' => 'nullable|string|max:20',
        'pic_email' => 'nullable|email|max:255',
    ]);

    $user = Auth::user();
    $visit = SalesVisit::findOrFail($id);

    // Check permission
    $userRoleName = strtolower($user->role->role_name ?? '');
    
    if ($userRoleName === 'sales' && $visit->sales_id !== $user->user_id) {
        return redirect()->back()->with('error', 'Anda tidak boleh mengedit data kunjungan milik sales lain.');
    }

    try {
        DB::beginTransaction();

        $picId = $request->pic_id;

        // Jika ada company_id tapi tidak ada pic_id, buat PIC baru
        if ($request->company_id && !$request->pic_id) {
            $companyPic = \App\Models\CompanyPic::create([
                'company_id' => $request->company_id,
                'pic_name' => $request->pic_name,
                'position' => $request->pic_position,
                'phone' => $request->pic_phone,
                'email' => $request->pic_email,
            ]);
            
            $picId = $companyPic->pic_id;
            \Log::info('✅ PIC baru dibuat saat update:', $companyPic->toArray());
        }

        $visit->update([
            'sales_id' => $request->sales_id,
            'user_id' => $user->user_id,
            'pic_name' => $request->pic_name,
            'pic_id' => $picId,
            'company_name' => $request->company_name ?? null,
            'company_id' => $request->company_id ?? null,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id ?? null,
            'district_id' => $request->district_id ?? null,
            'village_id' => $request->village_id ?? null,
            'address' => $request->address ?? null,
            'visit_date' => $request->visit_date,
            'visit_purpose' => $request->visit_purpose,
            'is_follow_up' => $request->boolean('is_follow_up') ?? false,
        ]);

        DB::commit();

        return redirect()->route('salesvisit')
            ->with('success', 'Data kunjungan sales berhasil diupdate!');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating sales visit: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Gagal mengupdate data: ' . $e->getMessage())
            ->withInput();
    }
}

public function destroy($id)
{
    $user = Auth::user();
    
    try {
        $visit = SalesVisit::find($id);
        
        if (!$visit) {
            return response()->json([
                'success' => false,
                'message' => 'Data kunjungan tidak ditemukan!'
            ], 404);
        }

        $userRoleName = strtolower($user->role->role_name ?? '');
        
        if ($userRoleName === 'sales' && $visit->sales_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak boleh menghapus data kunjungan milik sales lain.'
            ], 403);
        }

        $visit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan sales berhasil dihapus!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error deleting sales visit: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus data: ' . $e->getMessage()
        ], 500);
    }
}

public function getProvinces()
{
    try {
        $provinces = Province::orderBy('name', 'asc')
                            ->select('id', 'name')
                            ->get();
        
        \Log::info("Fetched provinces: " . $provinces->count());
        
        return response()->json([
            'success' => true,
            'provinces' => $provinces
        ]);
        
    } catch (\Exception $e) {
        \Log::error("Error fetching provinces: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to load provinces'
        ], 500);
    }
}

public function getRegencies($provinceId)
{
    try {
        if (empty($provinceId)) {
            return response()->json(['error' => 'Province ID required'], 400);
        }

        $provinceExists = Province::where('id', $provinceId)->exists();
        if (!$provinceExists) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $regencies = Regency::where('province_id', $provinceId)
                        ->orderBy('name', 'asc')
                        ->select('id', 'name', 'province_id')
                        ->get();
        
        \Log::info("Fetched regencies for province {$provinceId}: " . $regencies->count());
        
        return response()->json($regencies);
        
    } catch (\Exception $e) {
        \Log::error("Error fetching regencies: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

public function getDistricts($regencyId)
{
    try {
        if (empty($regencyId)) {
            return response()->json(['error' => 'Regency ID required'], 400);
        }

        $regencyExists = Regency::where('id', $regencyId)->exists();
        if (!$regencyExists) {
            return response()->json(['error' => 'Regency not found'], 404);
        }

        $districts = District::where('regency_id', $regencyId)
                        ->orderBy('name', 'asc')
                        ->select('id', 'name', 'regency_id')
                        ->get();
        
        \Log::info("Fetched districts for regency {$regencyId}: " . $districts->count());
        
        return response()->json($districts);
        
    } catch (\Exception $e) {
        \Log::error("Error fetching districts: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

public function getVillages($districtId)
{
    try {
        if (empty($districtId)) {
            return response()->json(['error' => 'District ID required'], 400);
        }

        $districtExists = District::where('id', $districtId)->exists();
        if (!$districtExists) {
            return response()->json(['error' => 'District not found'], 404);
        }

        $villages = Village::where('district_id', $districtId)
                        ->orderBy('name', 'asc')
                        ->select('id', 'name', 'district_id')
                        ->get();
        
        \Log::info("Fetched villages for district {$districtId}: " . $villages->count());
        
        return response()->json($villages);
        
    } catch (\Exception $e) {
        \Log::error("Error fetching villages: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt|max:5120',
    ]);

    try {
        $file = $request->file('file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        $header = array_shift($data);
        
        $imported = 0;
        $errors = [];

        DB::beginTransaction();

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2;
            
            try {
                if (count($row) < 8) {
                    $errors[] = "Row $rowNumber: Data tidak lengkap";
                    continue;
                }

                $sales = User::where('username', trim($row[1]))->first();
                if (!$sales) {
                    $errors[] = "Row $rowNumber: Sales '{$row[1]}' tidak ditemukan";
                    continue;
                }

                $province = Province::where('name', 'like', '%' . trim($row[4]) . '%')->first();
                if (!$province) {
                    $errors[] = "Row $rowNumber: Province '{$row[4]}' tidak ditemukan";
                    continue;
                }

                $regency = null;
                $district = null;
                $village = null;

                if (!empty(trim($row[5]))) {
                    $regency = Regency::where('province_id', $province->id)
                        ->where('name', 'like', '%' . trim($row[5]) . '%')
                        ->first();
                }

                if ($regency && !empty(trim($row[6]))) {
                    $district = District::where('regency_id', $regency->id)
                        ->where('name', 'like', '%' . trim($row[6]) . '%')
                        ->first();
                }

                if ($district && !empty(trim($row[7]))) {
                    $village = Village::where('district_id', $district->id)
                        ->where('name', 'like', '%' . trim($row[7]) . '%')
                        ->first();
                }

                $visitDate = null;
                if (!empty(trim($row[9]))) {
                    try {
                        $visitDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($row[9]))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $visitDate = date('Y-m-d');
                    }
                }

                $followUp = in_array(strtolower(trim($row[11] ?? '')), ['ya', 'yes', '1', 'true']);

                SalesVisit::create([
                    'sales_id' => $sales->user_id,
                    'user_id' => auth()->id(),
                    'pic_name' => trim($row[2]),
                    'pic_id' => null,
                    'company_name' => null,
                    'company_id' => null,
                    'province_id' => $province->id,
                    'regency_id' => $regency?->id,
                    'district_id' => $district?->id,
                    'village_id' => $village?->id,
                    'address' => trim($row[8]) ?: null,
                    'visit_date' => $visitDate ?: date('Y-m-d'),
                    'visit_purpose' => trim($row[10]),
                    'is_follow_up' => $followUp,
                ]);

                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }

        DB::commit();

        $message = "Berhasil import $imported data.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " data gagal: " . implode(', ', array_slice($errors, 0, 3));
        }

        return redirect()->route('salesvisit')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Gagal import: ' . $e->getMessage());
    }
}

private function getVisitActions($visit)
{
    $actions = [];

    if (!auth()->check()) {
        return $actions;
    }

    $canEdit = true;
    $canDelete = true;
    $canView = true;

    try {
        $currentMenuId = session('currentMenuId', 1);
        $canView = auth()->user()->canAccess($currentMenuId, 'view');
        $canEdit = auth()->user()->canAccess($currentMenuId, 'edit');
        $canDelete = auth()->user()->canAccess($currentMenuId, 'delete');
    } catch (\Exception $e) {
        \Log::error('Error checking permissions: ' . $e->getMessage());
    }

    // 🔥 Add View action
    if ($canView) {
        $actions[] = [
            'type' => 'view',
            'onclick' => "showVisitDetail('{$visit->id}')",
            'title' => 'Show Detail'
        ];
    }

    if ($canEdit) {
        $actions[] = [
            'type' => 'edit',
            'onclick' => "openEditVisitModal(" . json_encode([
                'id' => $visit->id,
                'salesId' => $visit->sales_id,
                'picName' => $visit->pic_name,
                'picId' => $visit->pic_id,
                'companyName' => $visit->company_name ?? '',
                'companyId' => $visit->company_id,
                'provinceId' => $visit->province_id,
                'regencyId' => $visit->regency_id,
                'districtId' => $visit->district_id,
                'villageId' => $visit->village_id,
                'address' => $visit->address ?? '',
                'visitDate' => $visit->visit_date->format('Y-m-d'),
                'purpose' => $visit->visit_purpose,
                'followUp' => $visit->is_follow_up ? 1 : 0
            ]) . ")",
            'title' => 'Edit Visit'
        ];
    }

    if ($canDelete) {
        $csrfToken = csrf_token();
        $deleteRoute = route('salesvisit.destroy', $visit->id);
        
        $actions[] = [
            'type' => 'delete',
            'onclick' => "deleteVisit('{$visit->id}', '{$deleteRoute}', '{$csrfToken}')",
            'title' => 'Delete Visit'
        ];
    }

    return $actions;
}

public function searchPics(Request $request)
{
    try {
        $search = $request->get('q');
        $companyId = $request->get('company_id');

        $query = \App\Models\CompanyPic::query();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($search) {
            $query->where('pic_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
        }

        $pics = $query->select('pic_id', 'pic_name', 'position', 'phone', 'email')
                     ->limit(10)
                     ->get();

        return response()->json([
            'success' => true,
            'pics' => $pics
        ]);

    } catch (\Exception $e) {
        \Log::error('Error searching PICs: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Server error'
        ], 500);
    }
}

public function storePic(Request $request)
{
    $request->validate([
        'company_id' => 'required|exists:company,company_id',
        'pic_name' => 'required|string|max:255',
        'pic_position' => 'nullable|string|max:255',
        'pic_phone' => 'nullable|string|max:20',
        'pic_email' => 'nullable|email|max:255',
    ]);

    try {
        DB::beginTransaction();

        $pic = \App\Models\CompanyPic::create([
            'company_id' => $request->company_id,
            'pic_name' => $request->pic_name,
            'position' => $request->pic_position,
            'phone' => $request->pic_phone,
            'email' => $request->pic_email,
        ]);

        DB::commit();

        \Log::info('PIC baru berhasil dibuat:', $pic->toArray());

        return response()->json([
            'success' => true,
            'message' => 'PIC berhasil ditambahkan!',
            'pic' => [
                'pic_id' => $pic->pic_id,
                'pic_name' => $pic->pic_name,
                'position' => $pic->position,
                'phone' => $pic->phone,
                'email' => $pic->email,
                'company_id' => $pic->company_id
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error storing PIC: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan PIC: ' . $e->getMessage()
        ], 500);
    }
}

}