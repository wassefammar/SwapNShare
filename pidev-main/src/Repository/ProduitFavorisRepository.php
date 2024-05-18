<?php

namespace App\Repository;

use App\Entity\ProduitFavoris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitFavoris>
 *
 * @method ProduitFavoris|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitFavoris|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitFavoris[]    findAll()
 * @method ProduitFavoris[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitFavorisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitFavoris::class);
    }

//    /**
//     * @return ProduitFavoris[] Returns an array of ProduitFavoris objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitFavoris
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
