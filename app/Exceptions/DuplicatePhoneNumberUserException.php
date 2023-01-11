<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DuplicatePhoneNumberUserException extends UnprocessableEntityHttpException
{

}
