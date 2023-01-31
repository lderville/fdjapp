<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {

        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findBy([],['name' => 'ASC'])
        ]);
    }



    #[Route('/newgame', name: 'new_game')]
    public function addGame( Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $game->setAddDate($now);
            $total = $form['ticketNumber']->getData()*$form['ticketPrice']->getData();
            $game->setTotalPrice($total);

            try {
                $entityManager->persist($game);
                $entityManager->flush();

            }catch (\Exception $exception){

            } finally {
                if(isset($exception)){
                    $this->addFlash('warning','Erreur le nouveau jeu n\'a pas été créé');
                }else{
                    $this->addFlash('success','Le nouveau jeu a bien été créé');
                }
            }

            return $this->redirectToRoute('game_index');
        }

        return $this->renderForm('game/newGame.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'game_edit')]
    public function editGame( Request $request, EntityManagerInterface $entityManager, $id, GameRepository $gameRepository): Response
    {
        $game = $gameRepository->findOneBy(['id'=>$id]);
        $form = $this->createForm(GameType::class, $game );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $game->setAddDate($now);
            $total = $form['ticketNumber']->getData()*$form['ticketPrice']->getData();
            $game->setTotalPrice($total);

            try {
                $entityManager->flush();

            }catch (\Exception $exception){

            } finally {
                if(isset($exception)){
                    $this->addFlash('warning','Erreur le nouveau jeu n\'a pas été créé');
                }else{
                    $this->addFlash('success','Le nouveau jeu a bien été créé');
                }
            }

            return $this->redirectToRoute('game_index');
        }

        return $this->renderForm('game/edit.html.twig', [
            'form' => $form,
            'game' => $game,
        ]);
    }

    #[Route('/deleteGame/{id}', name: 'game_delete')]
    public function deleteGame(EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->findOneBy(['id'=>$id]);
        foreach ($game->getBooks() as $book){

           $game->removeBook($book);
           $entityManager->remove($book);

        }

        $entityManager->remove($game);

        try {
            $entityManager->flush();

        }catch (\Exception $exception){

        } finally {
            if(isset($exception)){
                $this->addFlash('warning','Erreur le carnet n\'a pas été supprimé');
            }else{
                $this->addFlash('success','Le carnet a bien été supprimé');
            }
        }


        return $this->redirectToRoute('game_index', [

        ]);
    }


}
