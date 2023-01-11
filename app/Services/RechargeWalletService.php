<?php

namespace App\Services;

use App\Exceptions\DocumentUserNotFoundException;
use App\Repositories\UserRepository;

final class RechargeWalletService
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(array $data) : void
    {
        $user = $this->repository->find(['document' => $data['document'], 'phone_number' => $data['phone_number']]);

        if(!$user) {
            throw new DocumentUserNotFoundException('the field document not found.');
        }

        $this->repository->recharge([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'type' => 'recharge',
            'status' => 'confirmed'
        ]);
    }
}
