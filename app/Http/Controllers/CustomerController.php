<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    // GET page customers
    public function index()
    {
        $user = Auth::user();
        $provinces = Province::orderBy('name')->get();
        
        // Base query dengan relasi
        $customersQuery = Customer::with(['province', 'regency', 'district', 'village', 'user']);

        // 🔹 SUPERADMIN (role_id = 1) → lihat semua data
        if ($user->role_id == 1) {
            // tanpa filter apa pun
        }

        // 🔹 ADMIN (role_id = 7) & MARKETING (role_id = 11)
        //     → lihat semua data milik SALES (role_id = 12)
        elseif (in_array($user->role_id, [7, 11])) {
            $customersQuery->whereHas('user', function ($q) {
                $q->where('role_id', 12); // hanya user sales
            });
        }

        // 🔹 SALES (role_id = 12) → hanya data milik sendiri
        elseif ($user->role_id == 12) {
            $customersQuery->where('user_id', $user->user_id);
        }

        // kalau role lain (misalnya belum dikategorikan)
        else {
            $customersQuery->whereNull('id'); // tampil kosong aja
        }

        $customers = $customersQuery->paginate(10);

        return view('pages.customers', compact('provinces', 'customers'));
    }

    // GET all customers (AJAX) - WITH FILTERING
    public function customers()
    {
        $user = Auth::user();
        
        // Base query dengan relasi
        $query = Customer::with(['province', 'regency', 'district', 'village', 'user']);

        // 🔹 SUPERADMIN (role_id = 1) → lihat semua data
        if ($user->role_id == 1) {
            // tanpa filter apa pun
        }

        // 🔹 ADMIN (role_id = 7) & MARKETING (role_id = 11)
        //     → lihat semua data milik SALES (role_id = 12)
        elseif (in_array($user->role_id, [7, 11])) {
            $query->whereHas('user', function ($q) {
                $q->where('role_id', 12); // hanya user sales
            });
        }

        // 🔹 SALES (role_id = 12) → hanya data milik sendiri
        elseif ($user->role_id == 12) {
            $query->where('user_id', $user->user_id);
        }

        // kalau role lain
        else {
            $query->whereNull('id'); // tampil kosong
        }

        $customers = $query->get();
        return response()->json($customers);
    }

    // GET single customer (AJAX)
    public function show($id)
    {
        $customer = Customer::with(['province', 'regency', 'district', 'village', 'user'])->findOrFail($id);
        return response()->json($customer);
    }

    // Cascade Address Methods
    public function getRegencies($provinceId)
    {
        try {
            if (empty($provinceId)) {
                return response()->json(['error' => 'Province ID required'], 400);
            }

            $regencies = Regency::where('province_id', $provinceId)
                            ->orderBy('name', 'asc')
                            ->select('id', 'name', 'province_id')
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
                            ->select('id', 'name', 'regency_id')
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
                            ->select('id', 'name', 'district_id')
                            ->get();
            
            return response()->json($villages);
            
        } catch (\Exception $e) {
            \Log::error("Error fetching villages: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // POST create customer (AJAX)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Personal,Company',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'status' => 'required|in:Lead,Prospect,Active,Inactive',
            'source' => 'nullable|string|max:100',
            'pic' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        // Hanya role tertentu yang boleh tambah
        $allowedRoles = ['superadmin', 'admin', 'marketing', 'sales'];
        $userRoleName = strtolower($user->role->role_name ?? '');
        
        if (!in_array($userRoleName, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menambah customer.'
            ], 403);
        }

        // 🔹 Simpan user_id pembuat
        $validated['user_id'] = $user->user_id;

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan',
            'data' => $customer->load(['province', 'regency', 'district', 'village', 'user'])
        ], 201);
    }

    // PUT update customer (AJAX)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Personal,Company',
            'email' => 'required|email|unique:customers,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'status' => 'required|in:Lead,Prospect,Active,Inactive',
            'source' => 'nullable|string|max:100',
            'pic' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $customer = Customer::findOrFail($id);

        // 🔹 Sales hanya boleh edit data miliknya sendiri
        $userRoleName = strtolower($user->role->role_name ?? '');
        
        if ($userRoleName === 'sales' && $customer->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak boleh mengedit data milik sales lain.'
            ], 403);
        }

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diperbarui',
            'data' => $customer->load(['province', 'regency', 'district', 'village', 'user'])
        ]);
    }

    // DELETE customer (AJAX)
    public function destroy($id)
    {
        $user = Auth::user();
        $customer = Customer::findOrFail($id);

        // 🔹 Sales hanya bisa hapus data miliknya sendiri
        $userRoleName = strtolower($user->role->role_name ?? '');
        
        if ($userRoleName === 'sales' && $customer->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak boleh menghapus data milik sales lain.'
            ], 403);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
    }

    // BULK DELETE (AJAX)
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:customers,id'
        ]);

        $user = Auth::user();
        $userRoleName = strtolower($user->role->role_name ?? '');

        // 🔹 Sales hanya bisa hapus data miliknya sendiri
        if ($userRoleName === 'sales') {
            $customersToDelete = Customer::whereIn('id', $validated['ids'])
                                        ->where('user_id', $user->user_id)
                                        ->pluck('id')
                                        ->toArray();
            
            if (count($customersToDelete) !== count($validated['ids'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak boleh menghapus data milik sales lain.'
                ], 403);
            }
            
            Customer::whereIn('id', $customersToDelete)->delete();
            $count = count($customersToDelete);
        } else {
            // Superadmin, Admin, Marketing bisa hapus semua (sesuai filter view mereka)
            $count = count($validated['ids']);
            Customer::whereIn('id', $validated['ids'])->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $count . ' customer berhasil dihapus'
        ]);
    }
    // SEARCH & FILTER customers (AJAX)
public function search(Request $request)
{
    $user = Auth::user();
    
    // Base query dengan relasi
    $query = Customer::with(['province', 'regency', 'district', 'village', 'user']);

    // 🔹 Apply role-based filtering
    if ($user->role_id == 1) {
        // SUPERADMIN - lihat semua
    } elseif (in_array($user->role_id, [7, 11])) {
        // ADMIN & MARKETING - lihat data SALES
        $query->whereHas('user', function ($q) {
            $q->where('role_id', 12);
        });
    } elseif ($user->role_id == 12) {
        // SALES - hanya data sendiri
        $query->where('user_id', $user->user_id);
    } else {
        $query->whereNull('id');
    }

    // 🔍 Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('address', 'LIKE', "%{$search}%")
              ->orWhere('pic', 'LIKE', "%{$search}%")
              ->orWhere('contact_person_name', 'LIKE', "%{$search}%");
        });
    }

    // 🎯 Filter by Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🎯 Filter by Type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // 🎯 Filter by Province
    if ($request->filled('province')) {
        $query->where('province_id', $request->province);
    }

    // 🎯 Filter by Sales (hanya untuk Admin/Marketing/Superadmin)
    if ($request->filled('sales') && in_array($user->role_id, [1, 7, 11])) {
        $query->where('user_id', $request->sales);
    }

    $customers = $query->orderBy('created_at', 'desc')->get();

    return response()->json([
        'success' => true,
        'data' => $customers
    ]);
}

    // IMPORT dari CSV/Excel
    public function import(Request $request)
    {
        $user = Auth::user();

        // Hanya role tertentu yang boleh import
        $allowedRoles = ['superadmin', 'admin', 'marketing', 'sales'];
        $userRoleName = strtolower($user->role->role_name ?? '');
        
        if (!in_array($userRoleName, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk import data.'
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120'
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('imports', $fileName, 'local');

            $filePath = storage_path('app/' . $path);
            
            // Detect file type
            $ext = $file->getClientOriginalExtension();
            
            if ($ext === 'csv') {
                $rows = array_map('str_getcsv', file($filePath));
            } else {
                $rows = $this->readExcelFile($filePath);
            }

            if (empty($rows)) {
                Storage::delete($path);
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 400);
            }

            $header = array_shift($rows);
            $imported = 0;
            $errors = [];

            foreach ($rows as $key => $row) {
                if (empty(array_filter($row))) continue;

                try {
                    $data = array_combine($header, $row);
                    
                    if (!$data || empty($data['email'] ?? null)) {
                        $errors[] = "Baris " . ($key + 2) . ": Email tidak boleh kosong";
                        continue;
                    }

                    if (Customer::where('email', $data['email'])->exists()) {
                        $errors[] = "Baris " . ($key + 2) . ": Email sudah terdaftar";
                        continue;
                    }

                    Customer::create([
                        'customer_id' => $data['customer_id'] ?? null,
                        'name' => $data['name'] ?? 'N/A',
                        'type' => $data['type'] ?? 'Personal',
                        'email' => $data['email'],
                        'phone' => $data['phone'] ?? '',
                        'address' => $data['address'] ?? null,
                        'province_id' => $data['province_id'] ?? null,
                        'regency_id' => $data['regency_id'] ?? null,
                        'district_id' => $data['district_id'] ?? null,
                        'village_id' => $data['village_id'] ?? null,
                        'status' => $data['status'] ?? 'Lead',
                        'source' => $data['source'] ?? null,
                        'pic' => $data['pic'] ?? 'Admin',
                        'notes' => $data['notes'] ?? null,
                        'contact_person_name' => $data['contact_person_name'] ?? null,
                        'contact_person_email' => $data['contact_person_email'] ?? null,
                        'contact_person_phone' => $data['contact_person_phone'] ?? null,
                        'user_id' => $user->user_id, // 🔹 simpan user_id
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($key + 2) . ": " . $e->getMessage();
                }
            }

            Storage::delete($path);

            return response()->json([
                'success' => true,
                'message' => "$imported data customer berhasil diimpor",
                'imported' => $imported,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function readExcelFile($filePath)
    {
        $rows = [];
        // Implementasi baca Excel
        return $rows;
    }

    // EXPORT ke CSV
    public function export()
    {
        $user = Auth::user();
        
        // Base query dengan filter session (SAMA SEPERTI index/customers)
        $query = Customer::with(['province', 'regency', 'district', 'village', 'user']);

        // 🔹 SUPERADMIN (role_id = 1) → export semua data
        if ($user->role_id == 1) {
            // tanpa filter
        }

        // 🔹 ADMIN (role_id = 7) & MARKETING (role_id = 11)
        //     → export semua data milik SALES (role_id = 12)
        elseif (in_array($user->role_id, [7, 11])) {
            $query->whereHas('user', function ($q) {
                $q->where('role_id', 12);
            });
        }

        // 🔹 SALES (role_id = 12) → export hanya data milik sendiri
        elseif ($user->role_id == 12) {
            $query->where('user_id', $user->user_id);
        }

        // kalau role lain
        else {
            $query->whereNull('id');
        }

        $customers = $query->get();

        $filename = 'customers-' . date('Y-m-d-His') . '.csv';
        $handle = fopen('php://memory', 'w');

        // Header
        fputcsv($handle, [
            'ID', 'Customer ID', 'Nama', 'Tipe', 'Email', 'Telepon',
            'Alamat', 'Provinsi', 'Kabupaten', 'Kecamatan', 'Kelurahan',
            'Status', 'Source', 'PIC', 'Notes',
            'Contact Person Name', 'Contact Person Email', 'Contact Person Phone'
        ]);

        // Data
        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer->id,
                $customer->customer_id,
                $customer->name,
                $customer->type,
                $customer->email,
                $customer->phone,
                $customer->address,
                $customer->province->name ?? '',
                $customer->regency->name ?? '',
                $customer->district->name ?? '',
                $customer->village->name ?? '',
                $customer->status,
                $customer->source,
                $customer->pic,
                $customer->notes,
                $customer->contact_person_name,
                $customer->contact_person_email,
                $customer->contact_person_phone,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}