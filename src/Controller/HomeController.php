<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\DeckRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
use PhpParser\JsonDecoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

class HomeController extends AbstractController
{
    
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('home/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/allsets', name: 'all_sets')]
    public function allSets(): Response
    {
        $user = $this->getUser();

        return $this->render('cardSearch/allSets.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/set/{setCode}', name: 'show_set')]
    public function showSet(String $setCode): Response
    {
        
        return $this->render('cardSearch/showSet.html.twig', [

            'setCode' => $setCode
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/profile', name: 'app_profile')]
    public function seeProfile(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/alldecks', name: 'app_decks')]
    public function decksIndex(DeckRepository $deckRepository): Response
    {
        $decks = $deckRepository->findBy(['status' => 1]);

        return $this->render('decks/index.html.twig', [
            'decks' => $decks,
        ]);
    }

    #[Route('/deck/{id}', name: 'app_deck_consult', methods: ['GET', 'POST'])]
    public function deckBuild(Deck $deck, Comment $comment = null, DeckRepository $deckRepository, CompositionRepository $compositionRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $composition = $compositionRepository->findBy(['deck' => $deck]);
        $comments = $commentRepository->findBy(['deck' => $deck]);

        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class);

        // Handle the request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setCreationDate(new \DateTime()); // Set current date and time
            $comment->setDeck($deck);

            $formData = $form->getData();
            $textContent = $formData->getTextContent();

            $comment->setTextContent($textContent);

            $entityManager->persist($user);
            $entityManager->persist($deck);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_deck_consult', [
            'id' => $deck->getId()
        ]); 
        }


    return $this->render('decks/deckConsult.html.twig', [
        'deck' => $deck,
        'composition' => $composition,
        'user' => $user,
        'comments' => $comments,
        'commentForm' => $form,
        'comment' => $comment
    ]);
}

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

            return $this->redirectToRoute('app_deck_consult', ['id' => $deck->getId()]);
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
        

        return $this->redirectToRoute('app_deck_consult', ['id' => $deck->getId()]);
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
    
        return $this->redirectToRoute('app_deck_consult', ['id' => $deck->getId()]);
    }

    #[Route('/deck/{id}/likedremoved', name: 'remove_like_deck')]
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
    
        return $this->redirectToRoute('app_deck_consult', ['id' => $deck->getId()]);
    }

}
