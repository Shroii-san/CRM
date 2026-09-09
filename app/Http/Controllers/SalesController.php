<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Province; 
use App\Models\Regency;
use App\Models\District; 
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        $salesRole = Role::where('role_name', 'Sales')->first();
        
        if (!$salesRole) {
            return redirect()->back()->with('error', 'Role sales tidak ditemukan!');
        }
        
        $salesUsers = User::where('role_id', $salesRole->role_id)
            ->with(['province', 'regency', 'district', 'village', 'role'])
            ->paginate(5);
        $currentMenuId = view()->shared('currentMenuId', null);
        $provinces = Province::orderBy('name')->get();

        return view('pages.marketing', [
            'salesUsers' => $salesUsers,
            'salesRole' => $salesRole,
            'provinces' => $provinces,
            'currentMenuId' => $currentMenuId
        ]);
    }

    /**
     * Show sales user detail (AJAX)
     * GET /marketing/sales/{id}
     * Query parameters: year, month, start_date, end_date
     */
    public function show($id)
    {
        try {
            // Fetch user dengan semua relasi
            $user = User::with([
                'role',
                'province',
                'regency',
                'district',
                'village'
            ])->findOrFail($id);
            
            // Log untuk debug
            \Log::info('Sales Detail Fetched', [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'province' => $user->province ? $user->province->name : null,
                'has_province_id' => $user->province_id ? true : false,
            ]);
            
            // Get visit history dengan filtering
            $visitsQuery = \App\Models\SalesVisit::with([
                'company',
                'province',
                'regency',
                'district',
                'village'
            ])->where('sales_id', $id);

            // Apply filters jika ada
            if (request()->filled('year') && request()->filled('month')) {
                $year = request()->input('year');
                $month = request()->input('month');
                
                $visitsQuery->whereYear('visit_date', $year)
                           ->whereMonth('visit_date', $month);
                
                \Log::info('Applying month filter', [
                    'year' => $year,
                    'month' => $month,
                    'sales_id' => $id
                ]);
            } elseif (request()->filled('start_date') && request()->filled('end_date')) {
                $startDate = Carbon::parse(request()->input('start_date'))->startOfDay();
                $endDate = Carbon::parse(request()->input('end_date'))->endOfDay();
                
                $visitsQuery->whereBetween('visit_date', [$startDate, $endDate]);
                
                \Log::info('Applying date range filter', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'sales_id' => $id
                ]);
            }

            $visits = $visitsQuery->orderBy('visit_date', 'desc')
                                 ->limit(10)
                                 ->get()
                                 ->map(function($visit) {
                    $locationParts = [];
                    if ($visit->province) $locationParts[] = $visit->province->name;
                    if ($visit->regency) $locationParts[] = $visit->regency->name;
                    if ($visit->district) $locationParts[] = $visit->district->name;
                    if ($visit->village) $locationParts[] = $visit->village->name;
                    
                    return [
                        'visit_date' => $visit->visit_date ? $visit->visit_date->format('d-m-Y') : '-',
                        'company_name' => $visit->company ? $visit->company->company_name : ($visit->company_name ?? '-'),
                        'pic_name' => $visit->pic_name ?? '-',
                        'location' => count($locationParts) > 0 ? implode(', ', $locationParts) : '-',
                        'visit_purpose' => $visit->visit_purpose ?? '-',
                        'is_follow_up' => $visit->is_follow_up ? 'Ya' : 'Tidak'
                    ];
                });
            
            // Return response dengan data yang sudah dipastikan ada
            return response()->json([
                'success' => true,
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username ?? '-',
                    'email' => $user->email ?? '-',
                    'phone' => $user->phone ?? '-',
                    'birth_date' => $user->birth_date 
                        ? \Carbon\Carbon::parse($user->birth_date)->format('d-m-Y') 
                        : '-',
                    'role' => $user->role ? $user->role->role_name : '-',
                    'province' => $user->province ? $user->province->name : '-',
                    'province_id' => $user->province_id,
                    'regency' => $user->regency ? $user->regency->name : '-',
                    'regency_id' => $user->regency_id,
                    'district' => $user->district ? $user->district->name : '-',
                    'district_id' => $user->district_id,
                    'village' => $user->village ? $user->village->name : '-',
                    'village_id' => $user->village_id,
                    'address' => $user->address ?? '-',
                ],
                'visits' => $visits,
                'has_address' => $user->province_id != null && $user->regency_id != null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching sales detail', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address'   => 'nullable|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
        ]);

        $salesRole = Role::where('role_name', 'Sales')->first();

        User::create([
            'username'       => $request->username,
            'email'          => $request->email,
            'password_hash'  => Hash::make($request->password),
            'role_id'        => $salesRole->role_id,
            'phone'          => $request->phone,
            'birth_date'     => $request->birth_date,
            'address'        => $request->address,
            'province_id'    => $request->province_id,
            'regency_id'     => $request->regency_id,
            'district_id'    => $request->district_id,
            'village_id'     => $request->village_id,
            'is_active'      => true,
        ]);

        return redirect()->route('marketing')->with('success', 'Sales user berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $id . ',user_id',
            'email'    => 'nullable|email|unique:users,email,' . $id . ',user_id',
            'phone'    => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address'   => 'nullable|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Sales user berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('marketing')->with('success', 'Sales user berhasil dihapus!');
    }

    // CASCADE DROPDOWN METHODS
    
    public function getRegencies($provinceId)
    {
        try {
            if (empty($provinceId)) {
                return response()->json(['error' => 'Province ID required'], 400);
            }

            $regencies = Regency::where('province_id', $provinceId)
                               ->orderBy('name', 'asc')
                               ->get();
            
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

            $districts = District::where('regency_id', $regencyId)
                               ->orderBy('name', 'asc')
                               ->get();
            
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

            $villages = Village::where('district_id', $districtId)
                             ->orderBy('name', 'asc')
                             ->get();
            
            return response()->json($villages);
            
        } catch (\Exception $e) {
            \Log::error("Error fetching villages: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Search & Filter Sales (AJAX)
     */
    public function search(Request $request)
    {
        try {
            $salesRole = Role::where('role_name', 'Sales')->first();
            
            if (!$salesRole) {
                return response()->json(['error' => 'Role sales tidak ditemukan'], 404);
            }

            $query = User::with('role', 'province', 'regency', 'district', 'village')
                         ->where('role_id', $salesRole->role_id);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $isActive = $request->status === 'active' ? 1 : 0;
                $query->where('is_active', $isActive);
            }

            if ($request->filled('province_id')) {
                $query->where('province_id', $request->province_id);
            }

            $users = $query->paginate(10);

            $items = $users->map(function($user, $index) use ($users) {
                $alamatWilayah = collect([
                    $user->village ? $user->village->name : null,
                    $user->district ? $user->district->name : null,
                    $user->regency ? $user->regency->name : null,
                    $user->province ? $user->province->name : null,
                ])->filter()->implode(', ');
                
                $alamatDisplay = $alamatWilayah ?: ($user->address ?? '-');
                if ($alamatWilayah && $user->address) {
                    $alamatDisplay = $alamatWilayah . ' - ' . $user->address;
                }

                $actions = [];
                try {
                    $actions = $this->getSalesActions($user);
                } catch (\Exception $e) {
                    \Log::error('Error generating actions for user', [
                        'user_id' => $user->user_id,
                        'error' => $e->getMessage()
                    ]);
                }

                return [
                    'number' => $users->firstItem() + $index,
                    'user' => [
                        'username' => $user->username ?? '-',
                        'email' => $user->email ?? '-'
                    ],
                    'phone' => $user->phone ?? '-',
                    'date_birth' => $user->birth_date 
                        ? \Carbon\Carbon::parse($user->birth_date)->format('d-m-Y') 
                        : '-',
                    'alamat' => $alamatDisplay,
                    'role' => $user->role ? $user->role->role_name : 'No Role',
                    'status' => $user->is_active ? 'Active' : 'Inactive',
                    'actions' => $actions
                ];
            })->toArray();

            return response()->json([
                'items' => $items,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                    'total' => $users->total()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Sales Search Error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aksi untuk tiap baris Sales
     */
    private function getSalesActions($user)
    {
        $actions = [];

        if (!auth()->check()) {
            return $actions;
        }

        $canEdit = true;
        $canDelete = true;

        try {
            $currentMenuId = view()->shared('currentMenuId') ?? 1;
            $canEdit = auth()->user()->canAccess($currentMenuId, 'edit');
            $canDelete = auth()->user()->canAccess($currentMenuId, 'delete');
        } catch (\Exception $e) {
            \Log::error('Error checking permissions: ' . $e->getMessage());
            $canEdit = true;
            $canDelete = true;
        }

        if ($canEdit) {
            $actions[] = [
                'type' => 'edit', 
                'onclick' => "openEditSalesModal(
                    '{$user->user_id}',
                    '" . addslashes($user->username ?? '') . "',
                    '" . addslashes($user->email ?? '') . "',
                    '" . addslashes($user->phone ?? '') . "',
                    '" . addslashes($user->birth_date ?? '') . "',
                    '" . addslashes($user->address ?? '') . "',
                    '{$user->province_id}',
                    '{$user->regency_id}',
                    '{$user->district_id}',
                    '{$user->village_id}'
                )",
                'title' => 'Edit Sales'
            ];
        }

        if ($canDelete) {
            $csrfToken = csrf_token();
            $deleteRoute = route('marketing.sales.destroy', $user->user_id);
            
            $actions[] = [
                'type' => 'delete',
                'onclick' => "deleteSales('{$user->user_id}', '{$deleteRoute}', '{$csrfToken}')",
                'title' => 'Delete Sales'
            ];
        }

        return $actions;
    }
}