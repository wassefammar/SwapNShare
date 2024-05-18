<?php

namespace App\Repository;

use App\Entity\EchangeProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EchangeProduit>
 *
 * @method EchangeProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method EchangeProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method EchangeProduit[]    findAll()
 * @method EchangeProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchangeProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EchangeProduit::class);
    }

//    /**
//     * @return EchangeProduit[] Returns an array of EchangeProduit objects
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

//    public function findOneBySomeField($value): ?EchangeProduit
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
