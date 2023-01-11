<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InsufficientBalanceException extends UnprocessableEntityHttpException
{

}
