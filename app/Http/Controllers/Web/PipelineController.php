<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PipelineService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineController extends Controller
{
    public function __construct(private PipelineService $pipelineService)
    {
    }

    /**
     * Tampilkan halaman utama Pipeline Kanban Board.
     */
    public function index(Request $request): View
    {
        $pipelines = $this->pipelineService->list(activeOnly: true);

        return view('pages.pipeline', compact('pipelines'));
    }
}
