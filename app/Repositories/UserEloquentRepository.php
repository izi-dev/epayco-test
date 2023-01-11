<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SendCodePayNotification;

final class UserEloquentRepository implements UserRepository
{
    public function __construct(
        private User $model,
        private Transaction $transaction
    ) {}

    public function create(array $data) : void {
        $model = $this->model->newQuery()->create([
            'full_name' => $data['full_name'],
            'document' => $data['document'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
        ]);

        $model->wallet()->create([
            'amount' => 0
        ]);
    }

    public function find(array $filter) : object|null {
        return $this->model
            ->newQuery()
            ->with([
                'wallet.transactions'
            ])
            ->when(isset($filter['document']), fn($q) => $q->where('document', $filter['document']))
            ->when(isset($filter['phone_number']), fn($q) => $q->where('phone_number', $filter['phone_number']))
            ->when(isset($filter['email']), fn($q) => $q->where('email', $filter['email']))
            ->first();
    }

    public function recharge(array $data): void
    {
        $user = $this->model->newQuery()->find($data['user_id']);
        $wallet = $user->wallet;
        $wallet->transactions()->create([
            'token' => null,
            'code' => null,
            'amount' => $data['amount'],
            'type' => $data['type'],
            'status' => $data['status'],
        ]);
        $wallet->update([
            'amount' => $wallet->amount + $data['amount']
        ]);
    }

    public function pay(array $data): void
    {
        $user = $this->model->newQuery()->find($data['user_id']);
        $wallet = $user->wallet;
        $wallet->transactions()->create([
            'token' => $data['token'],
            'code' => $data['code'],
            'amount' => $data['amount'],
            'type' => $data['type'],
        ]);
    }

    public function notify(array $data): void
    {
        $user = $this->model->newQuery()->find($data['user_id']);
        $user->notify(new SendCodePayNotification($data['code'], $data['token']));
    }

    public function findTransactionBy(array $data): object|null
    {
        return $this->transaction->newQuery()
            ->where('code', $data['code'])
            ->where('token', $data['token'])
            ->first();
    }

    public function confirmed(array $data): void
    {
        $transaction = $this->transaction->newQuery()->find($data['transaction_id']);
        $transaction->update([
            'status' => 'confirmed',
            'token' => null,
            'code' => null,
        ]);
        $wallet = $transaction->wallet;
        $wallet->update([
            'amount' => $wallet->amount - $transaction->amount
        ]);
    }
}
