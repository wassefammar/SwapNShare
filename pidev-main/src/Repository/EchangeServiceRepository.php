<?php

namespace App\Repository;

use App\Entity\EchangeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EchangeService>
 *
 * @method EchangeService|null find($id, $lockMode = null, $lockVersion = null)
 * @method EchangeService|null findOneBy(array $criteria, array $orderBy = null)
 * @method EchangeService[]    findAll()
 * @method EchangeService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchangeServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EchangeService::class);
    }

//    /**
//     * @return EchangeService[] Returns an array of EchangeService objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EchangeService
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByDateRange(string $startDate, string $endDate): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->where('e.dateEchange >= :startDate')
            ->setParameter('startDate', new \DateTime($startDate));

        $qb->andWhere('e.dateEchange <= :endDate')
            ->setParameter('endDate', new \DateTime($endDate));

        return $qb->getQuery()->getResult();
    }

}
