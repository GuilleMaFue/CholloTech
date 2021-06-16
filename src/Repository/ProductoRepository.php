<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    /**
     * @return Producto[]
     */
    public function findAllBestCategorias(): array
    {

        return $this->createQueryBuilder('p')
            ->select('p, COUNT(p.id), c.id as categoriaID')
            ->innerJoin('p.categoria', 'c')
            ->groupBy('p.categoria')
            ->orderBy('COUNT(p.id)', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Producto[]
     */
    public function findAllBySearch($search): array
    {

        return $this->createQueryBuilder('p')
        ->select('p')
        ->where('p.nombre LIKE :busqueda')
        ->setParameter('busqueda', '%'. $search . '%')
        ->getQuery()
        ->getResult()
    ;
    }

    // /**
    //  * @return Producto[] Returns an array of Producto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Producto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
