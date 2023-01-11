<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DuplicateDocumentUserException extends UnprocessableEntityHttpException
{
}
