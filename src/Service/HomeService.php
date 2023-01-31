<?php

namespace App\Service;

use App\Repository\BookRepository;
use App\Repository\GameRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class HomeService
{
    protected $entityManager;
    protected $gamerepository;
    protected $bookrepository;
    protected $now;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        BookRepository $bookrepository,
        GameRepository $gamerepository,

    )
    {
        $this->entityManager = $entityManager;
        $this->bookrepository = $bookrepository;
        $this->gamerepository = $gamerepository;
    }

    function HomeUpdate($startDate, $endDate){


        //Pour une semaine particulier
        $books_added_by_date = $this->bookrepository->bookByWeekAdding($startDate, $endDate);
        //dd($books_added);

        $books_activated_by_date = $this->bookrepository->bookByWeekActivated($startDate, $endDate);
        //dd($books_activated);

        $books_billed_by_date = $this->bookrepository->bookByWeekBilling($startDate, $endDate);
        //dd($books_billed);

        //pour la semaine actuelle
        $now = new \DateTime();
        $now_last = new \DateTime();

        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $first_day = $now->setISODate((int)$now->format('o'), (int)$now->format('W'), 1);
        $first_day->setTime('00','00','00');

        $last_day = $now_last->setISODate((int)$now_last->format('o'), (int)$now_last->format('W'), 1)->modify("+ 6 day");
        $last_day->setTime(23, 59, 59);

        //dd($first_day->format('d/m/Y').' '.$last_day->format('d/m/Y'));

        $books_added_now = $this->bookrepository->bookByWeekAdding($first_day, $last_day);
        $books_activated_now = $this->bookrepository->bookByWeekActivated($first_day, $last_day);
        $books_billed_now = $this->bookrepository->bookByWeekBilling($first_day, $last_day);


        return [$books_added_by_date, $books_activated_by_date, $books_billed_by_date, $books_added_now, $books_activated_now, $books_billed_now];


    }





}