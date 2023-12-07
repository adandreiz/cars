<?php

namespace App\Service;

use App\Entity\Car;
use App\Entity\Colour;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
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

    public function createCar(?\stdClass $carData): Car
    {
        $this->validationService->validatePayload(['colourId'], $carData);

        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);

        $car = $serializer->denormalize(
            $carData,
            Car::class
        );

        // Wrap get colour in try and catch to return 422 instead of 404
        try {
            $colourRepository = $this->doctrine->getRepository(Colour::class);
            $colour = $colourRepository->findOrFail($carData->colourId);
        } catch (ExceptionService $exceptionData) {
            $exceptionData = new ExceptionDataService(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $exceptionData->getMessage()
            );
            throw new ExceptionService($exceptionData);
        }
        $car->setColour($colour);

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