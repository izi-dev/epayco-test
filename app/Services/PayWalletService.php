<?php

namespace App\Services;

use App\Exceptions\DocumentUserNotFoundException;
use App\Exceptions\InsufficientBalanceException;
use App\Repositories\UserRepository;
use Illuminate\Support\Str;

final class PayWalletService
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(array $data) : void
    {
        $user = $this->repository->find(['document' => $data['document'], 'phone_number' => $data['phone_number']]);

        if(!$user) {
            throw new DocumentUserNotFoundException('the field document not found.');
        }

        if($user->wallet->amount == 0 || ($user->wallet->amount - $data['amount']) < 0) {
            throw new InsufficientBalanceException('insufficient balance.');
        }

        $this->repository->pay([
            'user_id' => $user_id = $user->id,
            'amount' => $data['amount'],
            'type' => 'pay',
            'status' => 'pending',
            'token' => $token = Str::uuid(),
            'code' => $code = Str::random(6),
        ]);

        $this->repository->notify(compact('token', 'code', 'user_id'));
    }
}
