<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;

use App\Http\Controllers\Web\UserController as WebUserController;
use App\Http\Controllers\Api\V1\UserController as ApiUserController;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private WebUserController $webUserController,
        private ApiUserController $apiUserController
    ) {
    }

    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax() || $request->has('search') || $request->has('role_id')) {
            $indexRequest = IndexUserRequest::createFrom($request);
            return $this->apiUserController->index($indexRequest);
        }

        return $this->webUserController->index($request);
    }

    public function store(StoreUserRequest $request)
    {
        return $this->apiUserController->store($request);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        return $this->apiUserController->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->apiUserController->destroy($id);
    }
}

