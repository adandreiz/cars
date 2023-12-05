<?php

namespace App\Repository;

use App\Entity\Colour;
use App\Service\ServiceException;
use App\Service\ServiceExceptionData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Colour>
 *
 * @method Colour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colour[]    findAll()
 * @method Colour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colour::class);
    }

    public function findOrFail(int $id): Colour
    {
        $colour = $this->find($id);
        if (!$colour) {
            $exceptionData = new ServiceExceptionData(JsonResponse::HTTP_NOT_FOUND, 'Colour not found');
            throw new ServiceException($exceptionData);
        }
        return $colour;
    }

//    /**
//     * @return Colour[] Returns an array of Colour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Colour
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
