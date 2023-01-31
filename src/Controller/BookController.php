<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookEditType;
use App\Form\BookType;
use App\Form\CheckInvoiceType;
use App\Repository\BookRepository;
use App\Repository\GameRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/book')]
class BookController extends AbstractController
{

    #[Route('/newBook', name: 'new_book')]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {

        $book = new Book();
        $form = $this->createForm(BookType::class, $book );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setIsActivated(false);
            $book->setIsBilled(false);

            //current date
            $now = new \DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $book->setModificationdate($now);
            $book->setAddingDate($now);

            try {
                $entityManager->persist($book);
                $entityManager->flush();

            }catch (\Exception $exception){

            } finally {
                if(isset($exception)){
                    $this->addFlash('warning','Erreur le nouveau carnet n\'a pas été créé');
                }else{
                    $this->addFlash('success','Le nouveau carnet a bien été créé');
                }
            }

            return $this->redirectToRoute('new_book');
    }

        return $this->renderForm('book/newBook.html.twig', [
                'form' => $form,
        ]);

    }

    #[Route('/editBook/{id}', name: 'edit_book')]
    public function editBook(BookRepository $bookRepository, Request $request, EntityManagerInterface $entityManager,$id): Response
    {

        $book = $bookRepository->findOneBy(['id'=>$id]);
        $form = $this->createForm(BookEditType::class, $book );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //current date
            $now = new \DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $book->setModificationdate($now);
//                dd($form['activationDate'], $form['billingDate']);
            if ($form['activationDate']->getNormData() !== null){
                $book->setIsActivated(true);
            }else{

                $book->setIsActivated(false);
                $book->setActivationDate(null);
            }
            if ($form['billingDate']->getNormData() !== null){
                $book->setIsBilled(true);
            }else{
                $book->setIsBilled(false);
                $book->setBillingDate(null);
            }

            try {
                $entityManager->persist($book);
                $entityManager->flush();

            }catch (\Exception $exception){

            } finally {
                if(isset($exception)){
                    $this->addFlash('warning','Erreur le carnet n\'a pas été modifié');
                }else{
                    $this->addFlash('success','Le carnet a bien été modifié');
                }
            }

            return $this->redirectToRoute('archive_home');
        }

        return $this->renderForm('book/edit.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/getGame/{id}', name: 'get_game')]
    public function getGame(
        GameRepository $gameRepository,
        SerializerInterface $serializer,
        $id
    ): Response
    {
        $game = $gameRepository->findOneBy(['id'=>$id]);


        return $this->json($game, 200,[], [
            DateTimeNormalizer::FORMAT_KEY => 'd m Y ', 'groups'=>'group'
        ]);
    }



    #[Route('/activation', name: 'activation')]
    public function activationTwig(GameRepository $gameRepository, BookRepository $bookRepository
    ): Response
    {
        $games = $gameRepository->findBy(['isActivated'=>true]);
        $book_total_price = 0;
        $books = $bookRepository->findBy(['isActivated'=>false]);

        foreach ($books as $book){
            $book_total_price += $book->getGame()->getTotalPrice();

        }


        return $this->render('book/activation.html.twig', [
            'games' => $games,
            'books_price'=>$book_total_price
        ]);
    }

    #[Route('/activation/{id}', name: 'activation_game')]
    public function activationGame(
        GameRepository $gameRepository ,
        BookRepository $bookRepository,
        $id
    ): Response
    {
        $game = $gameRepository->findOneBy(['id'=>$id, 'isActivated'=>true]);


        $books = $bookRepository->findBy(['game'=>$game, 'isActivated'=>false, 'isBilled'=>false]);

        foreach ($books as $book){
            $book->setGame(null);
        }
        return $this->json($books, 200,[], [
            DateTimeNormalizer::FORMAT_KEY => 'd/m/Y ', 'activations'=>'activation'
        ]);
    }

    #[Route('/{id}/activation', name: 'activationbookID')]
    public function activationGameById(EntityManagerInterface $entityManager,BookRepository $bookRepository,$id
    ): Response
    {
        $book = $bookRepository->findOneBy(['id'=>$id]);

        $book->setIsActivated(true);

        //current date
        $now = new \DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $book->setActivationDate($now);
        $book->setModificationdate($now);

        try {
            $entityManager->persist($book);
            $entityManager->flush();

        }catch (\Exception $exception){

        } finally {
            if(isset($exception)){
                $this->addFlash('warning','Erreur le carnet n\'a pas été activé');
            }else{
                $this->addFlash('success','Le carnet a bien été activé');
            }
        }

        return $this->redirectToRoute('activation', [

        ]);
    }

    #[Route('/billing/{id}', name: 'billing_game')]
    public function billingGame(
        GameRepository $gameRepository ,
        BookRepository $bookRepository,
        SerializerInterface $serializer,
        $id
    ): Response
    {
        $game = $gameRepository->findOneBy(['id'=>$id, 'isActivated'=>true]);


        $books = $bookRepository->findBy(['game'=>$game, 'isActivated'=>true,'isBilled'=>false]);

        foreach ($books as $book){
            $book->setGame(null);
        }
        return $this->json($books, 200,[], [
            DateTimeNormalizer::FORMAT_KEY => 'd/m/Y ', 'activations'=>'activation'
        ]);
    }

    #[Route('/{id}/billing', name: 'billingbookID')]
    public function billingBookyId(EntityManagerInterface $entityManager,BookRepository $bookRepository,$id
    ): Response
    {
        $book = $bookRepository->findOneBy(['id'=>$id]);

        $book->setIsBilled(true);

        //current date
        $now = new \DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $book->setBillingDate($now);
        $book->setModificationdate($now);

        try {
            $entityManager->persist($book);
            $entityManager->flush();

        }catch (\Exception $exception){

        } finally {
            if(isset($exception)){
                $this->addFlash('warning','Erreur le carnet n\'a pas été facturé');
            }else{
                $this->addFlash('success','Le carnet a bien été facturé');
            }
        }

        return $this->redirectToRoute('activation', [

        ]);
    }

    #[Route('/billing', name: 'billing')]
    public function billingTwig( EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger, GameRepository $gameRepository,BookRepository $bookRepository
    ): Response
    {
        $games = $gameRepository->findBy(['isActivated'=>true]);
        $book_total_price = 0;
        $books = $bookRepository->findBy(['isActivated'=>true, 'isBilled'=>false]);


        $form = $this->createForm(CheckInvoiceType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $books_all =$bookRepository->findAll();
            $newFilename = null;
            $invoiceFile = $form->get('newInvoice')->getData();


            foreach ($books_all as $book){
                $book->setIsCheckBilling(null);
                $book->setIsCheckActivation(null);
            }

            $books_pdf = null;
            $games = $gameRepository->findAll();

            //Ajout du fichier

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($invoiceFile) {

                $originalFilename = pathinfo($invoiceFile, PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename.'-'.uniqid().'.'.$invoiceFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $invoiceFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

            }



            // Create an instance of the PDFParser
            $PDFParser = new Parser();

            // Create an instance of the PDF with the parseFile method of the parser
            // this method expects as first argument the path to the PDF file
            sleep(1);
            $pdf = $PDFParser->parseFile('./uploads/'.$newFilename);

            // Extract ALL text with the getText method
            $text_before = strtolower($pdf->getText());

            if(str_contains($text_before, "remboursement")){
                $text = substr($text_before, 0, strpos($text_before, "remboursement"));
            } else{
                $text = $text_before;
            }

            foreach ($games as $game) {


                preg_match('/' . strtolower($game->getRefBillingFdj()) . '\s*(\d+)/', $text, $matches);

                if (count($matches) !== 0){
                $books_find = $bookRepository->findBy(['isActivated' => true, 'game' => $game,'isBilled'=>false], ['activationDate' => 'ASC'], $matches[1]);

                if (intval($matches[1]) !== count($books_find)) {
                    unlink('./uploads/'.$newFilename);
                    $this->addFlash('warning', 'Il n\'y a pas assez de ' . $game->getName() . ' activés dans la Base de donnée');
                    return $this->redirectToRoute('billing');
                }
                foreach ($books_find as $book) {
                    $book->setIsCheckBilling(true);
                    $entityManager->persist($book);
                }
                }
            }


            $entityManager->flush();

            //$books_pdf = $bookRepository->findBy(['isActivated'=>true, 'isCheckBilling'=>true], ['game'=>'ASC']);
            $games = $gameRepository->findBooksforBilling();



            return $this->render('book/billingWithPdf.html.twig', [
                'games'=>$games,
                'fileName'=>$newFilename,

            ]);

        }

        foreach ($books as $book){
            $book_total_price += $book->getGame()->getTotalPrice();

        }

        return $this->renderForm('book/billing.html.twig', [
            'games' => $games,
            'books_price'=>$book_total_price,
            'form'=>$form
        ]);
    }




    #[Route('/deleteBook/{id}', name: 'book_delete')]
    public function deleteGame(EntityManagerInterface $entityManager, BookRepository $bookRepository, $id): Response
    {

        $book = $bookRepository->findOneBy(['id'=>$id]);


        try {
            $entityManager->remove($book);
            $entityManager->flush();

        }catch (\Exception $exception){

        } finally {
            if(isset($exception)){
                $this->addFlash('warning','Erreur le carnet n\'a pas été supprimé');
            }else{
                $this->addFlash('success','Le carnet a bien été supprimé');
            }
        }


        return $this->redirectToRoute('archive_home', [

        ]);
    }



}


