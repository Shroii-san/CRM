<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $employeeService)
    {
    }

    public function index()
    {
        $employees = Employee::with(['educationHistories', 'families'])->latest()->get();

        return view('employees', compact('employees'));
    }

    public function updateStatus(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'status' => ['required', 'integer'],
        ]);

        $candidate->update(['status' => $validated['status']]);

        if ($candidate->status === Candidate::STATUS_ACCEPTED) {
            $this->employeeService->promote($candidate);
        }

        return back();
    }
}
