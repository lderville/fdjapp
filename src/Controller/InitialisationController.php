<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Game;
use App\Repository\BookRepository;
use App\Repository\GameRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/init')]
class InitialisationController extends AbstractController
{
    #[Route('/initialisation', name: 'app_initialisation')]
    public function index(GameRepository $gameRepository, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        set_time_limit(0);
        $now = new \DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $file = './Init/init.csv';
        $csvfilesextension = pathinfo($file, PATHINFO_EXTENSION);
        $normalizer = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
        ];

        $serializer = new Serializer($normalizer,$encoders);
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $csvfilesextension);

        foreach ($data as $row){
            if (array_key_exists('Nom du jeu',$row) && !empty($row['Nom du jeu'])){


                $fdj_code = substr($row['Code emission'], 0, 3);


                ### cas particuliers
                if ($row['Code emission'] === '66802'){
                    $fdj_code = $row['Code emission'];
                }
                if ($row['Code emission'] === '66804'){
                    $fdj_code = $row['Code emission'];
                }
                ### fin de cas particuliers
                $game= $gameRepository->findOneBy(['refDeliveryFdj'=>$row['Nom du jeu'], 'codeFdj'=>$fdj_code]);


                if(!$game){
                    $game= new Game();
                    $game->setName($row['Nom du jeu']);
                    $game->setRefDeliveryFdj($row['Nom du jeu']);
                    $game->setIsActivated(true);
                    $game->setRefBillingFdj('inconnu');
                    $game->setAddDate($now);
                    $game->setTicketPrice(1);
                    $game->setTicketNumber(1);
                    $game->setTotalPrice(1);
                    $game->setCodeFdj(substr($row['Code emission'], 0, 3));



                    ###cas particuliers
                    if ($row['Code emission'] === '66802'){
                        $game->setCodeFdj($row['Code emission']);
                    }
                    if ($row['Code emission'] === '66804'){
                        $game->setCodeFdj($row['Code emission']);
                    }

                    ### Fin cas particuliers

                    $entityManager->persist($game);
                    $entityManager->flush();


                }
                $book = $bookRepository->findOneBy(['reference'=>$row['N° livret'],'game'=>$game]);
                if(!$book){

                    $newbook= new Book();

                    $newbook->setGame($game);
                    $newbook->setReference($row['N° livret']);
                    $newbook->setModificationdate($now);
                    //DateTimeImmutable::createFromFormat("Y-m-d", "2015-09-34");

                    $newbook->setAddingDate(DateTime::createFromFormat("Y-m-d H:i:s",$row['Date de réception']));

                    if (array_key_exists('Date d\'activation',$row) && !empty($row['Date d\'activation'])){
                        $newbook->setIsActivated(true);
                        $newbook->setActivationDate(DateTime::createFromFormat("Y-m-d H:i:s",$row['Date d\'activation']));
                    }else{
                        $newbook->setIsActivated(false);
                        $newbook->setActivationDate(null);
                    }

                    if (array_key_exists('Date statut vendu',$row) && !empty($row['Date statut vendu'])){
                        $newbook->setIsBilled(true);
                        $newbook->setBillingDate(DateTime::createFromFormat("Y-m-d H:i:s", $row['Date statut vendu']));
                    }else{
                        $newbook->setIsBilled(false);
                        $newbook->setBillingDate(null);
                    }



                    $entityManager->persist($newbook);
                    $entityManager->flush();


                }

            }
        }

        $file = './Init/game-init.csv';
        $csvfilesextension = pathinfo($file, PATHINFO_EXTENSION);

        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $csvfilesextension);

        foreach ($data as $row){
            if (array_key_exists('code',$row) && !empty($row['code'])){

//                dd($row['code'], $row['nom du jeu'], $row['QTE PAQT'], $row['VALEUR'], $row['PRIX CARNET'] );

                $game = $gameRepository->findOneBy(['codeFdj'=>$row['code']]);
                if ($game){
                    $game->setTicketNumber($row['QTE PAQT']);
                    $game->setTicketPrice($row['VALEUR']);
                    $game->setTotalPrice($row['QTE PAQT']*$row['VALEUR']);
                    $game->setName($row['nom du jeu']);
                    $game->setRefBillingFdj($row['ref facturation']);
                    $entityManager->persist($game);
                    $entityManager->flush();

                }

            }
        }

        return $this->redirectToRoute('app_home', [

        ]);
    }
}
