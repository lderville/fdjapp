<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Game;
use App\Form\CheckDeliveryType;
use App\Repository\BookRepository;
use App\Repository\GameRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/Delivery')]
class DeliveryController extends AbstractController
{


    #[Route('/ckeck', name: 'delivery_check')]
    public function check(Request $request, BookRepository $bookRepository, GameRepository $gameRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {

        $now = new \DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $form = $this->createForm(CheckDeliveryType::class, null);
        $newFilename = null;


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $games_added = [];
            $books_added = [];

            $delivery_file = $form->get('newDelivery')->getData();

            $books_all = $bookRepository->findAll();

            foreach ($books_all as $book) {
                $book->setIsCheckBilling(null);
                $book->setIsCheckActivation(null);
            }


            $games = $gameRepository->findAll();

            //Move File
            if ($delivery_file) {

                $originalFilename = pathinfo($delivery_file, PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $delivery_file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $delivery_file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $file = 'uploads/' . $newFilename;
                $csvfilesextension = pathinfo($file, PATHINFO_EXTENSION);
                $normalizer = [new ObjectNormalizer()];

                $encoders = [
                    new CsvEncoder(),
                ];

                $serializer = new Serializer($normalizer, $encoders);
                $fileString = file_get_contents($file);

                $data = $serializer->decode($fileString, $csvfilesextension);

                foreach ($data as $row) {
                    if (array_key_exists('Nom du jeu', $row) && !empty($row['Nom du jeu'])) {


                        $fdj_code = substr($row['Code emission'], 0, 3);


                        ### cas particuliers
                        if ($row['Code emission'] === '66802') {
                            $fdj_code = $row['Code emission'];
                        }
                        if ($row['Code emission'] === '66804') {
                            $fdj_code = $row['Code emission'];
                        }
                        ### fin de cas particuliers
                        $game = $gameRepository->findOneBy(['refDeliveryFdj' => $row['Nom du jeu'], 'codeFdj' => $fdj_code]);


                        if (!$game) {
                            $game = new Game();
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
                            if ($row['Code emission'] === '66802') {
                                $game->setCodeFdj($row['Code emission']);
                            }
                            if ($row['Code emission'] === '66804') {
                                $game->setCodeFdj($row['Code emission']);
                            }

                            ### Fin cas particuliers

                            $entityManager->persist($game);
                            $games_added[] = $game;
                            try {
                                $entityManager->flush();

                            } catch (\Exception $exception) {

                            }


                        }
                        $book = $bookRepository->findOneBy(['reference' => $row['N° livret'], 'game' => $game]);

                        if (!$book) {
                            $newbook = new Book();

                            $newbook->setGame($game);
                            $newbook->setReference($row['N° livret']);
                            $newbook->setModificationdate($now);
                            //DateTimeImmutable::createFromFormat("Y-m-d", "2015-09-34");

                            $newbook->setAddingDate(DateTime::createFromFormat("Y-m-d H:i:s", $row['Date de réception']));
                            $newbook->setActivationDate(null);
                            $newbook->setIsActivated(false);
                            $newbook->setBillingDate(null);
                            $newbook->setIsBilled(false);

                            $entityManager->persist($newbook);
                            $books_added[] = $newbook;

                            try {
                                $entityManager->flush();

                            } catch (\Exception $exception) {

                            }
                        }
                    }
                }

                return $this->render('delivery/DeliveryWithCsv.twig', [
                    'games_added' => $games_added,
                    'books_added' => $books_added,
                ]);
            }


        }
        return $this->renderForm('delivery/index.html.twig', [
            'form' => $form,
        ]);
    }
}