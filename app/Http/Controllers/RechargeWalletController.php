<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechargeWalletRequest;
use App\Services\RechargeWalletService;

class RechargeWalletController extends Controller
{
    public function __construct(private RechargeWalletService $service) {}

    public function __invoke(RechargeWalletRequest $request)
    {
        $this->service->__invoke($request->all());
    }
}
