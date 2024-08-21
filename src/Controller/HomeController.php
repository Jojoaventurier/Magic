<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\User;
use App\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
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

    #[Route('/deck/{id}', name: 'app_deck_consult')]
    public function deckBuild(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository): Response
    {
        $user = $this->getUser();
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $composition = $compositionRepository->findBy(['deck' => $deck]);
        $comments = [];


        return $this->render('decks/deckConsult.html.twig', [
            'deck' => $deck,
            'composition' => $composition,
            'user' => $user,
            'comments' => $comments
        ]);
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
