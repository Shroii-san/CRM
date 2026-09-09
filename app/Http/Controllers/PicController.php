<?php

namespace App\Http\Controllers;

use App\Models\CompanyPic;
use App\Models\Company;
use Illuminate\Http\Request;

class PicController extends Controller
{
    /**
     * List semua PIC
     */
    public function index()
    {
        $pics = CompanyPic::with('company')->paginate(5);
        $companies = Company::orderBy('company_name')->get();
        $currentMenuId = 10; // Sesuaikan dengan menu ID PIC di database
        
        return view('pages.pic', compact('pics', 'companies', 'currentMenuId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company,company_id',
            'pic_name'   => 'required|string|max:255',
            'position'   => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
        ]);

        CompanyPic::create([
            'company_id' => $request->company_id,
            'pic_name'   => $request->pic_name,
            'position'   => $request->position,
            'phone'      => $request->phone,
            'email'      => $request->email,
        ]);

        return redirect()->route('pic')->with('success', 'PIC berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $pic = CompanyPic::findOrFail($id);

        $request->validate([
            'company_id' => 'required|exists:company,company_id',
            'pic_name'   => 'required|string|max:255',
            'position'   => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
        ]);

        $pic->update([
            'company_id' => $request->company_id,
            'pic_name'   => $request->pic_name,
            'position'   => $request->position,
            'phone'      => $request->phone,
            'email'      => $request->email,
        ]);

        return redirect()->back()->with('success', 'PIC berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $pic = CompanyPic::findOrFail($id);
            $pic->delete();

            // Jika request AJAX, return JSON response
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PIC berhasil dihapus!'
                ]);
            }

            // Jika regular request, redirect
            return redirect()->route('pic')->with('success', 'PIC berhasil dihapus!');

        } catch (\Exception $e) {
            // Jika request AJAX, return JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus PIC: ' . $e->getMessage()
                ], 500);
            }

            // Jika regular request, redirect dengan error
            return redirect()->back()->with('error', 'Gagal menghapus PIC: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = CompanyPic::with('company');

        // Filter search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->where('pic_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Filter by company
        if ($request->filled('company')) {
            $companyId = $request->company;
            $query->where('company_id', $companyId);
        }

        $pics = $query->paginate(5);

        return response()->json([
            'items' => $pics->map(function($pic, $index) use ($pics) {
                return [
                    'number' => $pics->firstItem() + $index,
                    'pic' => [
                        'name' => $pic->pic_name ?? '-',
                        'position' => $pic->position ?? '-'
                    ],
                    'company' => $pic->company->company_name ?? '-',
                    'phone' => $pic->phone ?? '-',
                    'email' => $pic->email ?? '-',
                    'actions' => $this->getPicActions($pic)
                ];
            })->toArray(),
            'pagination' => [
                'current_page' => $pics->currentPage(),
                'last_page' => $pics->lastPage(),
                'from' => $pics->firstItem(),
                'to' => $pics->lastItem(),
                'total' => $pics->total()
            ]
        ]);
    }

    public function getPICsByCompany($companyId)
    {
        try {
            $pics = CompanyPic::where('company_id', $companyId)
                ->select('pic_id as id', 'pic_name as name', 'position', 'email', 'phone')
                ->orderBy('pic_name')
                ->get();
            
            \Log::info("Fetched PICs for company {$companyId}: " . $pics->count());
            
            return response()->json([
                'success' => true,
                'pics' => $pics
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error fetching PICs for company: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load PICs'
            ], 500);
        }
    }

    /**
     * Store PIC via AJAX (untuk modal di salesvisit)
     */
    public function storePICAjax(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:company,company_id',
                'pic_name'   => 'required|string|max:255',
                'position'   => 'nullable|string|max:255',
                'phone'      => 'nullable|string|max:20',
                'email'      => 'nullable|email|max:255',
            ]);

            $pic = CompanyPic::create([
                'company_id' => $request->company_id,
                'pic_name'   => $request->pic_name,
                'position'   => $request->position,
                'phone'      => $request->phone,
                'email'      => $request->email,
            ]);

            \Log::info('PIC created via AJAX:', $pic->toArray());

            return response()->json([
                'success' => true,
                'message' => 'PIC berhasil ditambahkan!',
                'pic' => [
                    'id' => $pic->pic_id,
                    'name' => $pic->pic_name,
                    'position' => $pic->position,
                    'email' => $pic->email,
                    'phone' => $pic->phone
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error storing PIC via AJAX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan PIC: ' . $e->getMessage()
            ], 500);
        }
    }


    private function getPicActions($pic)
    {
        $currentMenuId = 10; // Sesuaikan dengan menu ID
        $canEdit = auth()->user()->canAccess($currentMenuId, 'edit');
        $canDelete = auth()->user()->canAccess($currentMenuId, 'delete');

        $actions = [];

        if ($canEdit) {
            $actions[] = [
                'type' => 'edit',
                'onclick' => "openEditPICModal(
                    '{$pic->pic_id}',
                    '{$pic->company_id}',
                    '" . addslashes($pic->pic_name) . "',
                    '" . addslashes($pic->position ?? '') . "',
                    '" . addslashes($pic->email ?? '') . "',
                    '" . addslashes($pic->phone ?? '') . "'
                )",
                'title' => 'Edit PIC'
            ];
        }

        if ($canDelete) {
            $csrfToken = csrf_token();
            $deleteRoute = route('pics.destroy', $pic->pic_id);
            
            $actions[] = [
                'type' => 'delete',
                'onclick' => "deletePIC('{$pic->pic_id}', '{$deleteRoute}', '{$csrfToken}')",
                'title' => 'Delete PIC'
            ];
        }

        return $actions;
    }
}
