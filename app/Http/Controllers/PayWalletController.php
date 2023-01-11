<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayWalletRequest;
use App\Services\PayWalletService;

class PayWalletController extends Controller
{
    public function __construct(private PayWalletService $service) {}

    public function __invoke(PayWalletRequest $request)
    {
        $this->service->__invoke($request->all());
    }
}
