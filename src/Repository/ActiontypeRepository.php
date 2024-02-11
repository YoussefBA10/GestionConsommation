<?php

namespace App\Repository;

use App\Entity\Actiontype;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actiontype>
 *
 * @method Actiontype|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actiontype|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actiontype[]    findAll()
 * @method Actiontype[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiontypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actiontype::class);
    }

//    /**
//     * @return Actiontype[] Returns an array of Actiontype objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Actiontype
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
