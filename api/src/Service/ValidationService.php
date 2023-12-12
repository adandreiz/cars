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
            $exceptionData = new ExceptionDataService(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $errors);
            throw new ExceptionService($exceptionData);
        }
    }

}