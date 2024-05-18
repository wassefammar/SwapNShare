<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
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

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
// In your Symfony repository (e.g., ProduitRepository)

// In your Symfony repository (e.g., ProduitRepository)
public function searchProduitByPriceRanges( $priceRanges)
    {
        $em = $this->getEntityManager();

        
        [$min, $max] = explode('-', $priceRanges);

        return $em
        ->createQuery(
            'SELECT a from App\Entity\Produit a WHERE 
            a.prix BETWEEN ?1 AND ?2')
            ->setParameter(1,$min)
            ->setParameter(2,$max);
    }

    public function findProduit($requestString)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.titreProduit LIKE :val') // Using LIKE for partial matching
            ->setParameter('val', '%' . $requestString . '%') // Adding % to both sides of the string
            ->orderBy('e.id', 'ASC')
            ->getQuery();
    }

    public function findNameProduct($requestString)
    {
          $results= $this->createQueryBuilder('e')
            ->andWhere('e.titreProduit LIKE :val') // Using LIKE for partial matching
            ->setParameter('val', '%' . $requestString . '%') // Adding % to both sides of the string
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

            $suggestions = [];
            foreach ($results as $result) {
                $suggestions[] = $result->getTitreProduit();
            }

            return $suggestions;
    }

    public function findSortedByReviews()
    {
        // Custom DQL query to select products ordered by reviews
        $dql = '
            SELECT p
            FROM App\Entity\Produit p
            LEFT JOIN p.reviews r
            GROUP BY p
            ORDER BY AVG(r.note) DESC
        ';

        $query = $this->getEntityManager()->createQuery($dql);

        // Execute the query and return the result
        return $query;
    }

    public function findOrderedByDate()
    {
        // Custom DQL query to select products ordered by date (assuming 'date' is a field in Produit)
        $dql = '
            SELECT p
            FROM App\Entity\Produit p
            ORDER BY p.date DESC
        ';

        $query = $this->getEntityManager()->createQuery($dql);

        // Execute the query and return the result
        return $query;
    }
  

}




