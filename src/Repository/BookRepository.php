<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function bookByWeekActivated($date_start, $date_end)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.activationDate >= :valstart')
            ->setParameter('valstart', $date_start)
            ->andWhere('b.activationDate <= :valend')
            ->setParameter('valend', $date_end)
            ->orderBy('b.activationDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function bookByWeekAdding($date_start, $date_end)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.addingDate >= :valstart')
            ->setParameter('valstart', $date_start)
            ->andWhere('b.addingDate <= :valend')
            ->setParameter('valend', $date_end)
            ->orderBy('b.addingDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function bookByWeekBilling($date_start, $date_end)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.billingDate >= :valstart')
            ->setParameter('valstart', $date_start)
            ->andWhere('b.billingDate <= :valend')
            ->setParameter('valend', $date_end)
            ->orderBy('b.billingDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function bookFilter($reference, $is_activated, $is_not_activated, $is_billed, $is_not_billed, $modifdate, $game){


        $qb=$this->createQueryBuilder('b');

        if( $reference!==null  ) {
            $qb->andWhere('b.reference = :ref')
                ->setParameter('ref', $reference);
        }

        if( $is_activated === true  ) {

            $qb->andWhere('b.isActivated = :isActivated')
                ->setParameter('isActivated', true);
        }

        if( $is_not_activated === true  ) {
            $qb->andWhere('b.isActivated = :isNotActivated')
                ->setParameter('isNotActivated', false);

        }

        if( $is_billed === true  ) {

            $qb->andWhere('b.isBilled = :isBilled')
                ->setParameter('isBilled', true);
        }

        if( $is_not_billed === true  ) {
            $qb->andWhere('b.isBilled = :isNotBilled')
                ->setParameter('isNotBilled', false);
        }
        if( $modifdate !== null  ) {
            $qb->andWhere('b.modificationdate >= :modifdate')
                ->setParameter('modifdate', $modifdate->format('Y-m-d 00:00:00'));
        }
        if( $game !== null  ) {

            $qb->andWhere('b.game = :game')
                ->setParameter('game', $game);
        }


        $query=$qb->getQuery();

        return $query->getResult();

    }

    public function findByDate($year, $month)
    {
        if ($month === null) {
            $month = (int) date('m');
        }

        if ($year === null) {
            $year = (int) date('Y');
        }


        $startDate = new \DateTimeImmutable("$year-$month-01T00:00:00");
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('b');
        $qb->where('b.activationDate BETWEEN :start AND :end');
        $qb->setParameter('start', $startDate->format('Y-m-d H:i:s'));
        $qb->setParameter('end', $endDate->format('Y-m-d H:i:s'));
        $qb->andWhere('b.isActivated = :val');
        $qb->setParameter('val', true);
        return $qb->getQuery()->getResult();
    }

    public function CountBestSeller()
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.game','g', 'WITH', 'g.id = b.game')
            ->select('count(b)','g.name','g.codeFdj')
            ->where('b.isActivated = true')
            ->groupBy('b.game')
            ->addOrderBy('count(b)','DESC')
            ->getQuery()
            ->getResult()
            ;
    }



//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
