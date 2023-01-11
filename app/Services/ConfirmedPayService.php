<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TokenCodeNotFoundException;
use App\Repositories\UserRepository;

class ConfirmedPayService
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(array $data): void
    {
        $transaction = $this->repository->findTransactionBy([
            'code' => $data['code'],
            'token' => $data['token'],
        ]);

        if (!$transaction) {
            throw new TokenCodeNotFoundException('the token and code not found in pay.');
        }

        $wallet = $transaction->wallet;

        if($wallet->amount == 0 || ($wallet->amount - $transaction->amount) < 0) {
            throw new InsufficientBalanceException('insufficient balance.');
        }
        $this->repository->confirmed([
            'transaction_id' => $transaction->id
        ]);
    }
}
