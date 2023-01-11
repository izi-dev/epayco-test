<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAccountRequest;
use App\Services\GetUserAccountService;

class UserAccountController extends Controller
{
    public function __construct(private GetUserAccountService $service) {}

    public function __invoke(UserAccountRequest $request)
    {
        return $this->service->__invoke($request->all());
    }
}
