<?php

namespace App\Repository;

use App\Entity\Sale;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }



    public function countSalesBySeller(User $seller): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.user = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalRevenueBySeller(User $seller): float
    {
        return (float) $this->createQueryBuilder('s')
            ->select('SUM(s.total)')
            ->where('s.user = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // src/Repository/SaleRepository.php

    public function getAdminStats(): array
    {
        $qb = $this->createQueryBuilder('s');

        return [

            'totalRevenue' => $this->createQueryBuilder('s')
                ->select('SUM(s.total)')
                ->getQuery()
                ->getSingleScalarResult(),

            'averageSale' => $this->createQueryBuilder('s')
                ->select('AVG(s.total)')
                ->getQuery()
                ->getSingleScalarResult(),

            'salesThisMonth' => $this->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->where('s.createdAt >= :start')
                ->setParameter('start', (new \DateTime('first day of this month'))->setTime(0,0))
                ->getQuery()
                ->getSingleScalarResult(),
        ];
    }








}
