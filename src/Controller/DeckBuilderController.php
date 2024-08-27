<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\User;
use App\Form\DeckFormType;
use App\Entity\Composition;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
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

    #[Route('/build/deck/{id}', name: 'app_deck_builder')]
    public function deckBuild(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository): Response
    {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $composition = $compositionRepository->findBy(['deck' => $deck]);


        return $this->render('decks/deckBuilder.html.twig', [
            'deck' => $deck,
            'composition' => $composition 
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

    #[Route('/user/{user}/deck/{deck}/card', name: 'save_card_deck', methods: ['POST'])]
    public function saveCard(Deck $deck, Card $card = null, Composition $composition = null, EntityManagerInterface $entityManager, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, Request $request): Response 
    {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);

        $cardId = $request->get('cardId');
        $card = $cardRepository->findOneBy(['scryfallId' => $cardId]);
        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card]);
    
        if(!$card) {
            $card = new Card();
            $data = $request->get('cardData');
            $dataJS = json_decode($data, true); // true pour récupérer un tableau

            $entityManager->persist($card);

            $composition = new Composition();

            $card->setScryfallId($cardId);
            $card->setData($dataJS);
            $composition->setDeck($deck);
            $composition->setCard($card);
            $composition->setQuantity(1);
            $entityManager->persist($composition);

            } else if($card && !$composition) {

                    $composition = new Composition();
                    $composition->setQuantity(1);
                    $composition->setDeck($deck);
                    $composition->setCard($card);
                    $entityManager->persist($composition);
                    
                } else {
                    $quantity = $composition->getQuantity();
                    $quantity += 1;
                    $composition->setQuantity($quantity);
                    $entityManager->persist($composition);
            }     
 
        // $card->addDeck($deck);
        $entityManager->flush();
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }

    #[Route('/user/{user}/deck/{deck}/commander', name: 'save_commander_deck', methods: ['POST'])]
    public function saveCommander(Deck $deck, EntityManagerInterface $entityManager, DeckRepository $deckRepository, Request $request): Response 
    {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);
    

        $data = $request->get('cardData');
        $dataJS = json_decode($data, true); // true pour récupérer un tableau

        $deck->setCommander($dataJS);
        $entityManager->persist($deck);
        $entityManager->flush();
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }

    #[Route('/user/{user}/deck/{deck}/delete-commander', name: 'delete_commander', methods: ['POST', 'GET'])]
    public function deleteCommander(Deck $deck, EntityManagerInterface $entityManager, DeckRepository $deckRepository): Response 
    {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);
        $deck->setCommander(null);

        $entityManager->persist($deck);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }



    #[Route('/user/{user}/deck/{deck}/{card}/plus', name: 'plus_card_deck', methods: ['POST', 'GET'])]
    public function plusOne(Deck $deck, $card, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager, Request $request, ) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);

        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card]);

        $quantity = $composition->getQuantity();
        $quantity += 1;
        $composition->setQuantity($quantity);
        $entityManager->persist($composition);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }

    #[Route('/user/{user}/deck/{deck}/{card}/minus', name: 'minus_card_deck', methods: ['POST', 'GET'])]
    public function minusOne(Deck $deck, $card, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);


        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card]);

        $quantity = $composition->getQuantity();

        if($quantity > 1) {
            $quantity -= 1;
            $composition->setQuantity($quantity);
            $entityManager->persist($composition);

        } else if ($quantity == 1) {
            $entityManager->remove($composition);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }


    #[Route('/user/{user}/deck/{deck}/{card}/delete', name: 'delete_card_deck', methods: ['GET'])]
    public function deleteCardFromDeck(Deck $deck, $card, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {
        
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card]);

        $entityManager->remove($composition);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }

    #[Route('/user/{user}/deck/{deck}/deleteAllCards', name: 'delete_all_cards_deck', methods: ['GET'])]
    public function deleteAllCards(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $compositions = $compositionRepository->findBy(['deck' => $deck]);

        foreach($compositions as $composition) {
            $entityManager->remove($composition);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId()]);
    }

    #[Route('/user/{user}/deck/{deck}/delete', name: 'delete_deck', methods: ['GET'])]
    public function deleteDeck(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {

        $user = $this->getUser();
        $userId = $user->getId();
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $compositions = $compositionRepository->findBy(['deck' => $deck]);

        foreach($compositions as $composition) {
            $entityManager->remove($composition);
        }

        $entityManager->remove($deck);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_manager', ['id' => $userId]);

    }

}

        //TODO deck (exceptions commandant, affichages etc) -> systeme pour changer les fetch sur la recherche -> ajouter la recherche générale -> trouver un moyen d'afficher correctement les carte doubles faces -> boutons pour ajouter/supprimer une carte au deck
        //TODO FRONT FRONT FRONT 
        //TODO collection ? // page profil
        //TODO affichage double cartes sur toutes les vues : cardDetail
        //TODO pb : code javascript répété sur les vues 
        //TODO bug quand register -> utilisateur non connecté
