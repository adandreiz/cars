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
            $exceptionData = new ServiceExceptionData(JsonResponse::HTTP_UNPROCESSABLE_ENTITY,  $errors);
            throw new ServiceException($exceptionData);
        }
    }

    public function validatePayload(array $keys, \stdClass $data): void
    {
        foreach ($keys as $key) {
            if (!isset($data->$key)) {
                $exceptionData = new ServiceExceptionData(
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    sprintf('%s value is required', $key)
                );
                throw new ServiceException($exceptionData);
            }
        }
    }

}