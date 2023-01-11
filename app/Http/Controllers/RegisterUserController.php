<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Services\RegisterUserService;

class RegisterUserController extends Controller
{
    public function __construct(private RegisterUserService $service) {}

    public function __invoke(RegisterUserRequest $request)
    {
        $this->service->__invoke($request->all());
    }
}
