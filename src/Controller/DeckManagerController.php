<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\DeckFormType;
use App\Entity\Composition;
use App\Form\ImportDeckFormType;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Repository\StateRepository;
use App\Repository\FormatRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;    
    
    
   
class DeckManagerController extends AbstractController
{ 

    public function __construct(private HttpClientInterface $httpClient) {}

    #[Route('/deck/{deck}/comment/{comment}/edit', name: 'edit_comment')]
    public function editComment(Request $request, EntityManagerInterface $entityManager, Comment $comment, Deck $deck): Response
    {
        $user = $this->getUser();
        
        // Check if the logged-in user is the owner of the comment
        if ($comment->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres commentaires.');
        }
        
        // Create the form and pre-fill it with the existing comment data
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdateDate(new \DateTime()); // Set the update date
            $entityManager->persist($comment);
            $entityManager->flush(); // Save changes to the database

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
        }

        return $this->render('home/editComment.html.twig', [
            'commentForm' => $form->createView(),
            'deck' => $deck,
        ]);
    }

    #[Route('/deck/{deck}/comment/{comment}/delete', name: 'delete_comment', methods: ['POST'])]
    public function deleteComment(EntityManagerInterface $entityManager, Comment $comment, Deck $deck): Response
    {
        $user = $this->getUser();
        
            $entityManager->remove($comment);
            $entityManager->flush(); // Delete the comment from the database
        

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/deck/{id}/liked', name: 'like_deck')]
    public function addLike(Deck $deck, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un deck aux favoris.');
        }
    
        // Check if the user has already liked the deck
        if (!$user->getDecksLiked()->contains($deck)) {
            $user->addDecksLiked($deck);
    
            // Persist the changes
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/deck/{id}/likeremoved', name: 'remove_like_deck')]
    public function removeLike(Deck $deck, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour retirer un deck des favoris.');
        }
    
        // Check if the user has already liked the deck
        if ($user->getDecksLiked()->contains($deck)) {
            $user->removeDecksLiked($deck);
    
            // Persist the changes
            $entityManager->persist($user);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/user/{user}/deck/{deck}/export.csv', name: 'export_deck_csv', methods: ['GET'])]
    public function exportDeckAsCsv(Deck $deck, DeckRepository $deckRepository, StateRepository $stateRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $state = $stateRepository->findOneBy(['stateName' => "Mainboard"]);
        $compositions = $compositionRepository->findBy(['deck' => $deck, 'state' => $state]);

        $rows = [];
        foreach($compositions as $composition) {
            $card = $composition->getCard()->getData();
            $cardName = $card['name'];
 
            $data = [$composition->getQuantity(), $cardName];

            $rows[] = implode(' ', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

    #[Route('/user/{user}/deck/{deck}/export.txt', name: 'export_deck_txt', methods: ['GET'])]
    public function exportDeckAsTxt(Deck $deck, DeckRepository $deckRepository, StateRepository $stateRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $state = $stateRepository->findOneBy(['stateName' => "Mainboard"]);
        $compositions = $compositionRepository->findBy(['deck' => $deck, 'state' => $state]);

        $rows = [];
        foreach($compositions as $composition) {
            $card = $composition->getCard()->getData();
            $cardName = $card['name'];
 
            $data = [$composition->getQuantity(), $cardName];

            $rows[] = implode(' ', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.txt"');

        return $response;
    }

    // #[Route('/user/{user}/deck/import', name: 'import_deck_txt', methods: ['POST'])]
    // public function importDeck(StateRepository $stateRepository, CompositionRepository $compositionRepository, FormatRepository $formatRepository, EntityManagerInterface $entityManager, CardRepository $cardRepository, Request $request): Response
    // {
    //     $deck = new Deck();
    //     $deckFormat = $formatRepository->findOneBy(['formatName' => 'Commander / EDH']);
    //     $state = $stateRepository->findOneBy(['stateName' => 'MainBoard']);
    //     $deckForm = $request->get('import_deck_form');
    //     $deckName = $deckForm['deckName'];
    
    //     $deck->setCreationDate(new \DateTime());
    //     $deck->setFormat($deckFormat);
    //     $deck->setStatus(true);
    //     $deck->setDeckName($deckName);
    //     $deck->setUser($this->getUser());
    
    //     $entityManager->persist($deck);
    
    //     // Retrieve the text input from the textarea
    //     $deckList = $deckForm['deckList'];
    
    //     $lines = explode("\n", $deckList);
    
    //     $notFound = [];
    //     $addedCards = 0;
    //     $submittedCards = 0;
    
    //     foreach ($lines as $line) {
    //         $submittedCards++;
    
    //         // Match 'quantity card_name' format using regex
    //         if (preg_match('/^(\d+)\s+(.+)$/', trim($line), $matches)) {
    //             $quantity = (int) $matches[1];
    //             $cardName = $matches[2];
    
    //             // Fetch the card from Scryfall API
    //             $cardData = $this->fetchCardFromScryfall($cardName);
    
    //             if ($cardData) {
    //                 // Find or create the card
    //                 $card = $cardRepository->findOneBy(['scryfallId' => $cardData['id']]);
    //                 $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card, 'state' => $state]);
    //                 $card->saveCard();
    //                 // if (!$card) {
    //                 //     $card = new Card();
    //                 //     $card->setScryfallId($cardData['id']);
    //                 //     $card->setData($cardData);
    //                 //     $entityManager->persist($card);
    
    //                 //     $composition = new Composition();
    //                 //     $composition->setDeck($deck);
    //                 //     $composition->setCard($card);
    //                 //     $composition->setQuantity($quantity);
    //                 //     $composition->setState($state);
    //                 //     $entityManager->persist($composition);
    //                 //     $addedCards++;
    
    //                 // } else if ($card && !$composition) {
    //                 //     // Add composition for existing card
    //                 //     $composition = new Composition();
    //                 //     $composition->setQuantity($quantity);
    //                 //     $composition->setDeck($deck);
    //                 //     $composition->setCard($card);
    //                 //     $composition->setState($state);
    //                 //     $entityManager->persist($composition);
    //                 //     $addedCards++;
    
    //                 // } else if ($card && $composition) {
    //                 //     // Update quantity for existing composition
    //                 //     $composition->setQuantity($composition->getQuantity() + $quantity);
    //                 //     $entityManager->persist($composition);
    //                 //     $addedCards++;
    //                 // }
    //             } else {
    //                 $notFound[] = $cardName; // Add unfound card to list
    //             }
    //         }
    //     }
    //     // Save changes to the database
    //     $entityManager->flush();
    
    //     return $this->json([
    //         'status' => 'success',
    //         // 'redirect' => $this->generateUrl('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']),
    //         'redirect' => $this->generateUrl('app_deck_manager', ['id' => $this->getUser()->getId()]),
    //     ]);
    // }



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
        $importForm = $this->createForm(ImportDeckFormType::class);
        $importForm->handleRequest($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est soumis et valide
            // dd($topicForm->get('forumPost')->getData());
            $deck = $form->getData(); // on récupère les données du formulaire pour le titre du sujet qu'on stocke dans la variable $newTopic

            $deck->setCreationDate($currentDate); 
            $deck->setUser($user); // définit le user en tant
       

            $entityManager->persist($deck); // on prépare la requête d'ajout à la BDD
            $entityManager->flush(); // on exécute la requête

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
        }

        if($importForm->isSubmitted() && $importForm->isValid()) {
            return $this->redirectToRoute('import_deck_text');
        }

        return $this->render('decks/decksManager.html.twig', [
            'form' => $form,
            'userDecks' => $userDecks,
            'importDeckForm' => $importForm
        ]);
}



}
