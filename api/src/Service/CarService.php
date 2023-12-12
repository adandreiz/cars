<?php

namespace App\Service;

use App\Dto\CarDTO;
use App\Entity\Car;
use App\Entity\Colour;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function createCar(CarDTO $dto): Car
    {
        $car = new Car();
        try {
            $colour = $this->doctrine->getRepository(Colour::class)->findOrFail($dto->colourId);
        } catch (ExceptionService $exceptionData) {
            $exceptionData = new ExceptionDataService(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $exceptionData->getMessage()
            );
            throw new ExceptionService($exceptionData);
        }

        $car->setBuildDate(new \DateTimeImmutable($dto->buildDate))
            ->setModel($dto->model)
            ->setMake($dto->make)
            ->setColour($colour);
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