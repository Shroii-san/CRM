<?php

namespace App\Http\Controllers;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District; 
use App\Models\Village;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List semua user
     */
    public function index()
    {
        $users = User::with(['role', 'province', 'regency', 'district', 'village'])->paginate(5);
        $roles = Role::all(); 
        $provinces = Province::orderBy('name')->get();
        

        
        return view('pages.user', compact('users', 'roles', 'provinces'));
    }



   public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username',   
            'email'    => 'nullable|email|unique:users,email', 
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,role_id',
            'is_active' => 'sometimes|boolean',
            'phone'      => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'address'    => 'nullable|string|max:1000',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id'  => 'nullable|exists:regencies,id', 
            'district_id' => 'nullable|exists:districts,id',
            'village_id'  => 'nullable|exists:villages,id',
        ]);

        User::create([
            'username'       => $request->username,                        
            'email'          => $request->email,
            'password_hash'  => Hash::make($request->password),
            'phone'          => $request->phone,
            'birth_date'     => $request->birth_date,
            'address'        => $request->address,
            'role_id'        => $request->role_id,                        
            'is_active'      => $request->input('is_active', true),
            'province_id'    => $request->province_id,
            'regency_id'     => $request->regency_id,
            'district_id'    => $request->district_id,
            'village_id'     => $request->village_id,        
        ]);

        return redirect()->route('user')->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . $id . ',user_id', 
            'email'    => 'nullable|email|unique:users,email,' . $id . ',user_id', 
            'role_id'  => 'required|exists:roles,role_id',
            'is_active' => 'sometimes|boolean',
            'phone'      => 'nullable|string|max:20',                    
            'birth_date' => 'nullable|date|before_or_equal:today',        
            'address'    => 'nullable|string|max:1000',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id'  => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id', 
            'village_id'  => 'nullable|exists:villages,id',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'phone'          => $request->phone,
            'birth_date'     => $request->birth_date,
            'address'        => $request->address,
            'province_id'    => $request->province_id,
            'regency_id'     => $request->regency_id,
            'district_id'    => $request->district_id,
            'village_id'     => $request->village_id,
        ];

        
        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        
        return redirect()->back()->with('success', 'User berhasil diperbarui!');
    }
 
    public function getRegencies($provinceId)
        {
            try {
                // Validasi input
                if (empty($provinceId)) {
                    return response()->json(['error' => 'Province ID required'], 400);
                }

                // Cek apakah province exist
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

public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'User berhasil dihapus!');
    }
    



public function search(Request $request)
{
    $query = User::with('role', 'province', 'regency', 'district', 'village');

    // Filter search
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $query->where(function($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Filter role
    if ($request->filled('role')) {
        $role = $request->role;
        $query->whereHas('role', function($q) use ($role) {
            // Jika role adalah numeric ID
            if (is_numeric($role)) {
                $q->where('role_id', $role);
            } else {
                // Jika role adalah string name
                $q->whereRaw('LOWER(role_name) = ?', [strtolower($role)]);
            }
        });
    }

    $users = $query->paginate(5);

    return response()->json([
        'items' => $users->map(function($user, $index) use ($users) {
            // Format alamat
            $alamatWilayah = collect([
                optional($user->village)->name,
                optional($user->district)->name,
                optional($user->regency)->name,
                optional($user->province)->name,
            ])->filter()->implode(', ');
            
            $alamatDisplay = $alamatWilayah ?: ($user->address ?? '-');
            if ($alamatWilayah && $user->address) {
                $alamatDisplay = $alamatWilayah . ' - ' . $user->address;
            }

            return [
                'number' => $users->firstItem() + $index,
                'user' => [
                    'username' => $user->username ?? '-',
                    'email' => $user->email ?? '-'
                ],
                'phone' => $user->phone ?? '-',
                'date_birth' => $user->birth_date 
                    ? \Carbon\Carbon::parse($user->birth_date)->format('d M Y') 
                    : '-',
                'alamat' => $alamatDisplay,
                'role' => $user->role->role_name ?? 'No Role',
                'status' => $user->is_active ? 'Active' : 'Inactive',
                'actions' => $this->getUserActions($user)
            ];
        })->toArray(),
        'pagination' => [
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'from' => $users->firstItem(),
            'to' => $users->lastItem(),
            'total' => $users->total()
        ]
    ]);
}

private function getUserActions($user)
{
    $isSuperAdmin = auth()->user()->role && 
                   (strtolower(auth()->user()->role->role_name) === 'superadmin');
    
    $isTargetSuperAdmin = $user->role && 
                         (strtolower($user->role->role_name) === 'superadmin');
    
    $canEdit = auth()->user()->canAccess($currentMenuId ?? 1, 'edit') && 
              (!$isTargetSuperAdmin || $isSuperAdmin);
    
    $canDelete = auth()->user()->canAccess($currentMenuId ?? 1, 'delete') && 
                (!$isTargetSuperAdmin || $isSuperAdmin);

    $actions = [];

    if ($canEdit) {
        $actions[] = [
            'type' => 'edit', 
            'onclick' => "openEditModal(
                '{$user->user_id}',
                '" . addslashes($user->username) . "',
                '" . addslashes($user->email) . "',
                '{$user->role_id}',
                " . ($user->is_active ? 'true' : 'false') . ",
                '" . addslashes($user->phone ?? '') . "',
                '" . addslashes($user->birth_date ?? '') . "',
                `" . addslashes($user->address ?? '') . "`,
                '{$user->province_id}',
                '{$user->regency_id}',
                '{$user->district_id}',
                '{$user->village_id}'
            )",
            'title' => 'Edit User'
        ];
    }

    if ($canDelete) {
        $csrfToken = csrf_token();
        $deleteRoute = route('users.destroy', $user->user_id);
        
                $actions[] = [
            'type' => 'delete',
            'onclick' => "deleteUser('{$user->user_id}', '{$deleteRoute}', '{$csrfToken}')",
            'title' => 'Delete User'
        ];
    }

    return $actions;
}
}