<?php

namespace App\Service;

class ServiceExceptionData
{

    public function __construct(protected int $statusCode, protected string $message)
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message
        ];
    }
}
