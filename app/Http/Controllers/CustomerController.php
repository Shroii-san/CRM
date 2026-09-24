<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\ClientController as WebClientController;
use App\Http\Controllers\Api\V1\ClientController as ApiClientController;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private ClientService $clientService,
        private WebClientController $webClientController,
        private ApiClientController $apiClientController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search')) {
            return $this->apiClientController->index($request);
        }

        return $this->webClientController->index($request);
    }

    public function customers(Request $request)
    {
        return $this->apiClientController->index($request);
    }

    public function store(StoreClientRequest $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiClientController->store($request);
        }

        $this->clientService->create($request->validated());
        return redirect()->route('customers')->with('success', 'Client berhasil ditambahkan');
    }

    public function update(UpdateClientRequest $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return $this->apiClientController->update($request, (int) $id);
        }

        $this->clientService->update((int) $id, $request->validated());
        return redirect()->route('customers')->with('success', 'Client berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->clientService->delete((int) $id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Client deleted successfully']);
        }

        return redirect()->route('customers')->with('success', 'Client berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        return $this->apiClientController->bulkDelete($request);
    }

    public function search(Request $request)
    {
        return $this->apiClientController->index($request);
    }
}