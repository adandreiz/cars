<?php

namespace App\Controller;

use App\Dto\CarDTO;
use App\Entity\Car;
use App\Repository\CarRepository;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarService $carService;

    private CarRepository $carRepository;

    public function __construct(
        CarService $carService,
        CarRepository $carRepository)
    {
        $this->carService = $carService;
        $this->carRepository = $carRepository;
    }

    #[Route('/cars', name: 'add_cars', methods:'POST')]
    public function addCars(#[MapRequestPayload] CarDTO $car): JsonResponse
    {
        $car = $this->carService->createCar($car);
        return $this->json(['message' => sprintf('Car %s %s buit on %s colour %s created with id %s',
            $car->getMake(), $car->getModel(), $car->getBuildDate()->format('d-m-Y'), $car->getColour()->getName(), $car->getId())],
            JsonResponse::HTTP_CREATED);
    }

    #[Route('/car/{id}', name: 'get_car', methods:'GET')]
    public function getCar(int $id): JsonResponse
    {
        $car = $this->carRepository->findOrFail($id);
        return $this->json($car);
    }

    #[Route('/cars/{id}', name: 'delete_car', methods:'DELETE')]
    public function deleteCar(int $id): JsonResponse
    {
        $car = $this->carRepository->findOrFail($id);
        $this->carService->deleteCar($car);
        return $this->json(['message' => sprintf('Car with id %s deleted', $id)],JsonResponse::HTTP_OK);
    }

    #[Route('/cars', name: 'list_cars', methods:'GET')]
    public function listCars(): JsonResponse
    {
        // @TODO accept params page, perPage, sortBy and sortDirection to paginate results
        $cars = $this->carRepository->findAll();
        return $this->json($cars);
    }

}
