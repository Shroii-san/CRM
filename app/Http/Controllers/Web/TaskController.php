<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    /**
     * Tampilkan halaman utama Task Management (Blade View).
     */
    public function index(Request $request): View
    {
        $tasks = $this->taskService->list(user: $request->user());

        return view('pages.task', compact('tasks'));
    }
}
