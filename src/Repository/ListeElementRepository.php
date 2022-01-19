<?php

namespace App\Repository;

use App\Entity\ListeElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListeElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListeElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListeElement[]    findAll()
 * @method ListeElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListeElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeElement::class);
    }

    // /**
    //  * @return ListeElement[] Returns an array of ListeElement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListeElement
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
