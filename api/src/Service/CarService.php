<?php

namespace App\Service;

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

    public function createCar(\stdClass $carData): Car
    {
        // Validate all required keys are set
        $this->validationService->validatePayload(['make','model','colourId','buildDate'], $carData);

        // Wrap get colour in try and catch to return 422 instead of 404
        try {
            $colourRepository = $this->doctrine->getRepository(Colour::class);
            $colour = $colourRepository->findOrFail($carData->colourId);
        } catch (ServiceException $exceptionData) {
            $exceptionData = new ServiceExceptionData(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $exceptionData->getMessage()
            );
            throw new ServiceException($exceptionData);
        }

        // @TODO Use and configure and use JMS deserializer
        $car = new Car();
        $car->setMake($carData->make)
            ->setModel($carData->model)
            ->setBuildDate(new \DateTimeImmutable($carData->buildDate))
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