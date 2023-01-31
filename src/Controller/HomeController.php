<?php

namespace App\Controller;

use App\Form\ArchiveType;
use App\Repository\BookRepository;
use App\Repository\GameRepository;
use App\Service\HomeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class HomeController extends AbstractController
{
    #[Route('/recap', name: 'app_recap')]
    public function index( HomeService $homeService,GameRepository $gameRepository): Response
    {

        $games = $gameRepository->findAll();

        $week_end = null;
        $week_start = null;

        $now_first = new \DateTime();
        $now_last = new \DateTime();
        $last_week_date = $now_first->modify("-7 day");
        $last_week_date_end = $now_last->modify("-7 day");

        $week_start = $last_week_date->setISODate((int)$last_week_date->format('o'), (int)$last_week_date->format('W'), 1);
        $week_start->setTime('00','00','00');

        $week_end = $last_week_date_end->setISODate((int)$last_week_date_end->format('o'), (int)$last_week_date_end->format('W'), 1)->modify("+ 6 day");
        $week_end->setTime('23','59','59');


        $mybooks = $homeService->HomeUpdate($week_start, $week_end);


        return $this->render('home/recap.html.twig', [
            'mybooks'=>$mybooks,
            'week_start'=>$week_start,
            'week_end'=>$week_end,
            'games'=>$games

        ]);
    }

    #[Route('/Choose', name: 'choose_home')]
    public function weekChoose(HomeService $homeService, GameRepository $gameRepository): Response
    {

        $games = $gameRepository->findAll();
        $mybooks = null;
        $week_start=null;
        $week_end = null;

        if (isset($_POST['valid'])) {
            $data_select = filter_input(INPUT_POST, 'weekChoose', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $week = explode('-W',$data_select);

            $week_start = new \DateTime();
            $week_end = new \DateTime();
            $week_start->setISODate($week[0],$week[1]);
            $week_start->setTime(00, 00, 00);
            $week_end->setTime(23, 59, 59);
            $week_end->setISODate($week[0],$week[1])->modify("+ 6 day");

            //dd($week_start->format('Y-m-d H:i:s')." // ".$week_end->format('Y-m-d H:i:s'));

            $mybooks = $homeService->HomeUpdate($week_start, $week_end);

            /* detail de mybooks
            0->date paramètre added
            1->date paramètre activated
            2->date paramètre billed
            3->date now added
            4->date now activated
            5->date now billed
            */

        }


        return $this->render('home/recap.html.twig', [
            'mybooks'=>$mybooks,
            'week_start'=>$week_start,
            'week_end'=>$week_end,
            'games'=>$games

        ]);
    }

    #[Route('/Archive', name: 'archive_home')]
    public function Archive(Request $request, BookRepository $bookRepository): Response
    {

        $archive =null;
        $is_not_activated = null;

        $is_not_billed = null;



        $form = $this->createForm(ArchiveType::class, $archive );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $is_not_activated = $form->get('isNotActivated')->getData();
            $is_not_billed = $form->get('isNotBilled')->getData();
            $reference = $form['reference']->getdata();
            $modifdate = $form['modificationdate']->getdata();
            $is_activated = $form['isActivated']->getdata();
            $is_billed = $form['isBilled']->getdata();
            $game = $form['game']->getdata();


            // dd($reference, $is_activated, $is_not_activated, $is_billed, $is_not_billed,$modifdate, $game);
            $books = $bookRepository->bookFilter($reference, $is_activated, $is_not_activated, $is_billed, $is_not_billed,$modifdate, $game);


            //($reference, $is_activated, $is_not_activated, $is_billed, $is_not_billed,$modifdate, $game);
            return $this->renderForm('home/archive.html.twig', [
                'form' => $form,
                'books' => $books,

            ]);
        }

        return $this->renderForm('home/archive.html.twig', [
            'form' => $form,

        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(GameRepository $gameRepository, BookRepository $bookRepository): Response
    {

        $books = $bookRepository->findAll();
        $games = $gameRepository->findAll();


        return $this->render('home/index.html.twig', [
            'book'=>$books,
            'games'=>$games,
        ]);
    }


    #[Route('/graphique/data', name: 'data_graph')]
    public function dataGraph( BookRepository $bookRepository): Response
    {
        $data = null;
        $mydata = null;
        $books_first = $bookRepository->findOneBy(['isActivated'=>true], ['activationDate'=>'ASC'] );
        $smallest_date = $books_first->getActivationDate()->format(('Y'));
        $books_last = $bookRepository->findOneBy(['isActivated'=>true], ['activationDate'=>'DESC'] );
        $biggest_date = $books_last->getActivationDate()->format(('Y'));

        foreach (range($smallest_date, $biggest_date) as $number) {
            $data['years'][$number] = $number;
        }



        foreach ($data['years'] as $year){
            for($i=1; $i<=12; $i++) {
                $books= $bookRepository->findByDate($year, $i);
                $tot = 0;
                $tot_count = 0;
                if (empty($books)) $mydata['years'][$year][$i] = 0;
                if (empty($books)) $mydata['count'][$year][$i] = 0;


                foreach ($books as $book){
                    $tot += $book->getGame()->getTotalPrice();
                    $mydata['years'][$year][$i] = $tot;
                    $tot_count += $book->getGame()->getTicketNumber();
                    $mydata['count'][$year][$i] = $tot_count;

                }
            }
        }

        $books = $bookRepository->CountBestSeller();
        function searchCodeFdj($code, $array): int|string|null
        {
            foreach ($array as $key => $val) {
                if ($val['codeFdj'] === $code) {

                    return $key;
                }
            }
            return null;
        }

        $total_books_activated = count($bookRepository->findBy(['isActivated'=>true]));
        //rechercher les 2 valeurs super200 dans les carnets
        $super200i1 = searchCodeFdj(66802,$books);
        $super200i2 = searchCodeFdj(66804,$books);
        $cash1 = searchCodeFdj(648 ,$books);
        $cash2 = searchCodeFdj(140 ,$books);

        //fusionner les 2 valeurs en 1
        $super200[1] = $books[$super200i1][1] + $books[$super200i2][1];
        $super200['name'] = 'SUPER200';
        $super200['codeFdj'] = 668;
        $books[$super200i1] = $super200;
        unset($books[$super200i2]);

        $cash[1] = $books[$cash1][1] + $books[$cash2][1];
        $cash['name'] = 'CASH';
        $cash['codeFdj'] = 140;
        $books[$cash1] = $cash;
        unset($books[$cash2]);
        //trier
        usort($books, fn($a, $b) => $a[1] <= $b[1]);

        $mydata['bestSeller'][0]=$books[0];
        $mydata['bestSeller'][1]=$books[1];
        $mydata['bestSeller'][2]=$books[2];
        $mydata['bestSeller'][3]=$books[3];
        $mydata['bestSeller'][4]=[1=>$total_books_activated-$books[0][1]-$books[1][1]-$books[2][1]-$books[3][1],'name'=>'Autres'];

        return $this->json($mydata, 200,[], [

        ]);
    }


    #[Route('/graph', name: 'app_graph')]
    public function graph(): Response
    {

        return $this->render('home/graph.html.twig', [

        ]);
    }


}

