<?php

namespace App\Services;

use App\Exceptions\DocumentUserNotFoundException;
use App\Repositories\UserRepository;

class GetUserAccountService
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(array $data): array
    {
        $user = $this->repository->find([
            'document' => $data['document'],
            'phone_number' => $data['phone_number'],
        ]);

        if (!$user) {
            throw new DocumentUserNotFoundException('the field document not found.');
        }

        return $user->toArray();
    }
}
