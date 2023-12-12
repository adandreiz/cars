<?php

namespace App\Serializer;

use App\Entity\Car;
use App\Repository\ColourRepository;
use App\Service\ExceptionDataService;
use App\Service\ExceptionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CarDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private ColourRepository $colourRepository;

    public function __construct(ColourRepository $colourRepository)
    {
        $this->colourRepository = $colourRepository;
    }

    /**
     * @inheritDoc
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        try {
            // Check for keys
            if (isset($data['colour']) && isset($data['colour']['id'])) {
                $colour = $this->colourRepository->findOrFail($data['colour']['id']);
            } else {
                $exceptionData = new ExceptionDataService(
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    'Please provide a colour'
                );
                throw new ExceptionService($exceptionData);
            }
        } catch (ExceptionService $exceptionData) {
            $exceptionData = new ExceptionDataService(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                $exceptionData->getMessage()
            );
            throw new ExceptionService($exceptionData);
        }
        $car = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        $car->setColour($colour);

        return $car;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Car::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Car::class => true
        ];
    }


}