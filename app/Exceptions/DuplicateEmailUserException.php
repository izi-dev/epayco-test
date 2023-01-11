<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DuplicateEmailUserException extends UnprocessableEntityHttpException
{

}
