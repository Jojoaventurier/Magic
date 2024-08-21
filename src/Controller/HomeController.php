<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Repository\DeckRepository;
use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $composition = $compositionRepository->findBy(['deck' => $deck]);


        return $this->render('decks/deckConsult.html.twig', [
            'deck' => $deck,
            'composition' => $composition 
        ]);
    }

}
