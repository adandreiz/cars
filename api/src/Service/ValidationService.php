<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @param $entity
     * @return void
     */
    public function validateEntity($entity): void
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            preg_match_all('/Object\(App\\\\Entity\\\\(\w+)\)\.(\w+):\n\s+(.*?) \(/', $errors, $matches, PREG_SET_ORDER);
            $msg = [];
            foreach ($matches as $match) {
                $msg[] = sprintf('%s: %s', $match[2], $match[3]);
            }
            $exceptionData = new ExceptionDataService(JsonResponse::HTTP_UNPROCESSABLE_ENTITY,  implode("\n",$msg));

            throw new ExceptionService($exceptionData);
        }
    }

    public function validatePayload(array $keys, \stdClass $data): void
    {
        foreach ($keys as $key) {
            if (!isset($data->$key)) {
                $exceptionData = new ExceptionDataService(
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    sprintf('%s value is required', $key)
                );
                throw new ExceptionService($exceptionData);
            }
        }
    }

}