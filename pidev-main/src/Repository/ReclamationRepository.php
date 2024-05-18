<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    //    /**
    //     * @return Reclamation[] Returns an array of Reclamation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reclamation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function orderByUrgencyAsc(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.urgence', 'ASC') // Trie par date croissante
            ->getQuery()
            ->getResult();
    }

    public function orderByUrgencyDesc(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.urgence', 'DESC') // Trie par date croissante
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.date', 'ASC') // Trie par date croissante
            ->getQuery()
            ->getResult();
    }

    public function search_urgence($requestString)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.urgence LIKE :val') // Using LIKE for partial matching
            ->setParameter('val', '%' . $requestString . '%') // Adding % to both sides of the string
            ->orderBy('r.urgence', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedByUrgency()
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('FIELD(r.urgence, \'critical\', \'urgent\', \'high\', \'normal\', \'low\')', 'DESC');

        return $qb->getQuery()->getResult();
    }

    
    public function findOrderedByDate(): array
    { // Custom DQL query to select products ordered by date (assuming 'date' is a field in Produit) 
        $dql = ' SELECT p FROM App\Entity\Reclamation p ORDER BY p.date DESC ';
        $query = $this->getEntityManager()->createQuery($dql);
        // Execute the query and return the result
        return $query->getResult();
    }
}
