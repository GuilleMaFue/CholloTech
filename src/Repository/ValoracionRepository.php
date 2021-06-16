<?php

namespace App\Repository;

use App\Entity\Valoracion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Valoracion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Valoracion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Valoracion[]    findAll()
 * @method Valoracion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValoracionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Valoracion::class);
    }

    /**
     * @return Valoracion[]
     */
    public function findAllValoraciones(): array
    {

        return $this->createQueryBuilder('v')
            ->select('v, avg(v.puntuacion) as puntuacion, p.id as productoID')
            ->innerJoin('v.producto', 'p')
            ->groupBy('v.producto')
            ->orderBy('AVG(v.puntuacion)', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Valoracion[] Returns an array of Valoracion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Valoracion
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
