<?php

namespace App\Repository;

use App\Entity\PaymentOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentOperation[]    findAll()
 * @method PaymentOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentOperation::class);
    }

    public function getAmountForLastMonthByUsername(string $username)
    {
        $firstDayOfMonth = new \DateTime('midnight first day of this month');

        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount) as total_amount')
            ->where('p.username = :username')
            ->andWhere('p.date >= :start_date')
            ->groupBy('p.username')
            ->setParameter('username', $username)
            ->setParameter('start_date', $firstDayOfMonth)
            ->getQuery();

            return $result->getOneOrNullResult();
    }

    // /**
    //  * @return PaymentOperation[] Returns an array of PaymentOperation objects
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
    public function findOneBySomeField($value): ?PaymentOperation
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
