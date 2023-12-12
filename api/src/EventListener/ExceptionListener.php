<?php

namespace App\EventListener;

use App\Service\ExceptionService;
use App\Service\ExceptionDataService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ExceptionService) {
            $exceptionData = $exception->getExceptionData();
        } elseif ($exception instanceof HttpException) {
            $exceptionData = new ExceptionDataService($exception->getCode(), $exception->getPrevious()->getMessage());
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $exceptionData = new ExceptionDataService($statusCode, $exception->getMessage());
        }

        $response = new JsonResponse($exceptionData->toArray());
        $event->setResponse($response);
    }
}