<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmedPayRequest;
use App\Services\ConfirmedPayService;

class ConfirmedPayController extends Controller
{
    public function __construct(private ConfirmedPayService $service) {}

    public function __invoke(ConfirmedPayRequest $request)
    {
        $this->service->__invoke($request->all());
    }
}
