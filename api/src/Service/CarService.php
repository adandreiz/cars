<?php

namespace App\Service;

use App\Entity\Car;
use App\Entity\Colour;
use App\Serializer\CarDenormalizer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CarService
{
    private ManagerRegistry $doctrine;

    private ValidationService $validationService;

    public function __construct(
        ManagerRegistry $doctrine,
        ValidationService $validationService
    ) {
        $this->doctrine = $doctrine;
        $this->validationService = $validationService;
    }

    public function createCar(string $carData): Car
    {
        try {
            $encoder = new JsonEncoder();
            $serializer = new Serializer(
                [new DateTimeNormalizer(), new ObjectNormalizer(null, null, null, new ReflectionExtractor())],
                [$encoder]
            );
            $car = $serializer->deserialize($carData, Car::class, 'json');
            $serializer = new Serializer([new CarDenormalizer($this->doctrine->getRepository(Colour::class))], [$encoder]);
            $serializer->deserialize(
                $carData,
                Car::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $car
                ]
            );
        } catch (ExceptionInterface $e) {
            $exceptionData = new ExceptionDataService(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
            throw new ExceptionService($exceptionData);
        }

        $this->validationService->validateEntity($car);

        $em = $this->doctrine->getManager();
        $em->persist($car);
        $em->flush();
        return $car;
    }

    public function deleteCar(Car $car): void
    {
        $em = $this->doctrine->getManager();
        $em->remove($car);
        $em->flush();
    }

}