<?php

namespace App\Service;

class ExceptionDataService
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
        preg_match_all('/Object\(App\\\\(?:Dto|Entity)\\\\(\w+)\)\.(\w+):\n\s+(.*?) \(/', $this->message, $matches, PREG_SET_ORDER);
        if (count($matches)) {
            foreach ($matches as $match) {
                $msg[] = sprintf('%s: %s', $match[2], $match[3]);
            }
            $msg = implode("\n",$msg);
        } else {
            $msg = $this->message;
        }

        return [
            'message' => $msg
        ];
    }
}
