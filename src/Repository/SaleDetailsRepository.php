<?php

namespace App\Repository;

use App\Entity\SaleDetails;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaleDetails>
 */
class SaleDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleDetails::class);
    }

    public function getTopProductsBySeller(User $seller, int $limit = 5): array
    {
        return $this->createQueryBuilder('d')
            ->select('p.name AS productName, SUM(d.quantity) AS totalQuantity, SUM(d.quantity * d.unitPrice) AS totalRevenue')
            ->join('d.product', 'p')
            ->join('d.sale', 's')
            ->where('s.user = :seller')
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult();
    }

}
