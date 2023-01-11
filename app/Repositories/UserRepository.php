<?php

namespace App\Repositories;

interface UserRepository
{
    public function create(array $data): void;
    public function find(array $filter): object|null;
    public function recharge(array $data): void;
    public function pay(array $data): void;
    public function confirmed(array $data): void;
    public function notify(array $data): void;
    public function findTransactionBy(array $data): object|null;
}
