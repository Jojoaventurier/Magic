<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\User;
use App\Form\DeckFormType;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeckBuilderController extends AbstractController
{
    #[Route('/{id}/deck/manager', name: 'app_deck_manager')]
    public function index(Deck $deck = null, EntityManagerInterface $entityManager, Request $request, DeckRepository $deckRepository, User $user): Response
    {
        $currentDate = new \DateTime();// récupère la date actuelle
        $user = $this->getUser(); // récupère le user en session

        $userDecks = $deckRepository->findBy(['user' => $user ]);

        if(!$deck) {
            $deck = new Deck;
        }

        $form = $this->createForm(DeckFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est soumis et valide
            // dd($topicForm->get('forumPost')->getData());
            $deck = $form->getData(); // on récupère les données du formulaire pour le titre du sujet qu'on stocke dans la variable $newTopic

            $deck->setCreationDate($currentDate); 
            $deck->setUser($user); // définit le user en tant
       

            $entityManager->persist($deck); // on prépare la requête d'ajout à la BDD
            $entityManager->flush(); // on exécute la requête

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
        }

        return $this->render('decks/decksManager.html.twig', [
            'form' => $form,
            'userDecks' => $userDecks
        ]);
}

    #[Route('/deck/{id}', name: 'app_deck_builder')]
    public function deckBuild(Deck $deck, DeckRepository $deckRepository): Response
    {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);

        return $this->render('decks/deckBuilder.html.twig', [
            'deck' => $deck,
        ]);
    }
 
    
    #[Route('/search', name: 'app_search')]
    public function advancedSearch(): Response
    {
       $user =  $this->getUser();
        
        return $this->render('cardSearch/cardSearch.html.twig', [
            'user' => $user
        ]);
    }


    #[Route('/card/{cardId}', name: 'app_card_detail')]
    public function cardDetail(Request $request): Response
    {
        $cardId = $request->get('cardId');
        
        return $this->render('cardSearch/cardDetail.html.twig', [
            'cardId' => $cardId
        ]);
    }

    #[Route('/user/{user}/deck/{deck}/card/{cardId}/{imageUrl}', name: 'save_card_deck')]
    public function saveCard(Deck $deck, Card $card = null, EntityManagerInterface $entityManager, DeckRepository $deckRepository, CardRepository $cardRepository, Request $request): Response 
    {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);

        if(!$card) {
            $card = new Card();
        }
        $cardId = $request->get('cardId');
        // $imageUrl = $request->get('imageUrl');
        $card->setScryfallId($cardId);
        // $card->setNormalImageUrl($imageUrl);

        $card->addDeck($deck);

        $entityManager->persist($card);
        $entityManager->persist($deck); 
        $entityManager->flush();

         
        return $this->render('decks/deckBuilder.html.twig', [
            'deck' => $deck,
        ]);
    }


    // #[Route("/card/{id}", name="card_detail")}
    // public function detail($id): Response
    // {
    //     // Récupère les détails de la carte en fonction de l'ID
    //     $card = [
    //         'id' => $id,
    //         'name' => 'Nom de la carte ' . $id,
    //         'description' => 'Description de la carte ' . $id,
    //         // autres détails
    //     ];

    //     return $this->render('card_detail.html.twig', [
    //         'card' => $card,
    //     ]);
    // }
}
