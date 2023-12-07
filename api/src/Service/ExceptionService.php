<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionService extends HttpException
{
    private ExceptionDataService $exceptionData;

    public function __construct(ExceptionDataService $exceptionData)
    {
        $statusCode = $exceptionData->getStatusCode();
        $message = $exceptionData->getMessage();

        parent::__construct($statusCode, $message);
        $this->exceptionData = $exceptionData;
    }

    public function getExceptionData(): ExceptionDataService
    {
        return $this->exceptionData;
    }
}