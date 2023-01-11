<?php

namespace App\Services;

use App\Exceptions\DuplicateDocumentUserException;
use App\Exceptions\DuplicateEmailUserException;
use App\Exceptions\DuplicatePhoneNumberUserException;
use App\Repositories\UserRepository;

final class RegisterUserService
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(array $data) : void
    {
        $this->validate($data);
        $this->repository->create($data);
    }

    private function validate(array $data): void
    {
        if($this->repository->find(['document' => $data['document']])) {
            throw new DuplicateDocumentUserException('the field document is duplicate.');
        }

        if($this->repository->find(['email' => $data['email']])) {
            throw new DuplicateEmailUserException('the field email is duplicate.');
        }

        if($this->repository->find(['phone_number' => $data['phone_number']])) {
            throw new DuplicatePhoneNumberUserException('the field phone_number is duplicate.');
        }
    }
}
